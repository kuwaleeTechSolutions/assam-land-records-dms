from __future__ import annotations

import io
from functools import lru_cache

from fastapi import Depends, FastAPI, File, Form, Header, HTTPException, UploadFile
from PIL import Image, UnidentifiedImageError

from .inference import AssameseHtrEngine
from .settings import settings

app = FastAPI(title="Assamese Land Record HTR", version="1.0.0")


@lru_cache(maxsize=1)
def engine() -> AssameseHtrEngine:
    return AssameseHtrEngine(settings.model_path, settings.device)


def authorize(authorization: str | None = Header(default=None)) -> None:
    if not settings.api_token:
        return
    expected = f"Bearer {settings.api_token}"
    if authorization != expected:
        raise HTTPException(status_code=401, detail="Invalid HTR service token.")


@app.get("/health")
def health() -> dict:
    try:
        loaded = engine()
        return {
            "status": "ready",
            "model_path": settings.model_path,
            "device": str(loaded.device),
        }
    except Exception as exc:
        raise HTTPException(status_code=503, detail=str(exc)) from exc


@app.post("/v1/recognize", dependencies=[Depends(authorize)])
async def recognize(
    image: UploadFile = File(...),
    language: str = Form(default="asm"),
    document_domain: str = Form(default="assam_land_records"),
) -> dict:
    if language != "asm":
        raise HTTPException(status_code=422, detail="This service accepts Assamese handwriting only.")

    content = await image.read()
    if len(content) > settings.max_image_mb * 1024 * 1024:
        raise HTTPException(status_code=413, detail="Image exceeds configured size limit.")

    try:
        source = Image.open(io.BytesIO(content))
    except UnidentifiedImageError as exc:
        raise HTTPException(status_code=422, detail="Uploaded file is not a valid image.") from exc

    try:
        result = engine().recognize(source)
    except Exception as exc:
        raise HTTPException(status_code=503, detail=str(exc)) from exc

    return {
        "text": result.text,
        "confidence": result.confidence,
        "metadata": {
            **result.metadata,
            "language": language,
            "document_domain": document_domain,
            "original_filename": image.filename,
        },
    }
