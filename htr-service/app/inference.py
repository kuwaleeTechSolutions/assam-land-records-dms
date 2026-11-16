from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path
from typing import Any

import torch
from PIL import Image, ImageOps
from transformers import AutoImageProcessor, VisionEncoderDecoderModel, AutoTokenizer


@dataclass
class RecognitionResult:
    text: str
    confidence: float
    metadata: dict[str, Any]


class AssameseHtrEngine:
    def __init__(self, model_path: str, device: str = "cpu") -> None:
        path = Path(model_path)
        if not path.exists():
            raise RuntimeError(
                f"HTR model was not found at {path}. Train or copy a compatible "
                "VisionEncoderDecoder model before starting the service."
            )

        self.device = torch.device(device)
        self.processor = AutoImageProcessor.from_pretrained(path)
        self.tokenizer = AutoTokenizer.from_pretrained(path)
        self.model = VisionEncoderDecoderModel.from_pretrained(path).to(self.device)
        self.model.eval()

    @torch.inference_mode()
    def recognize(self, image: Image.Image) -> RecognitionResult:
        image = ImageOps.exif_transpose(image).convert("RGB")
        inputs = self.processor(images=image, return_tensors="pt")
        pixel_values = inputs.pixel_values.to(self.device)

        output = self.model.generate(
            pixel_values,
            max_new_tokens=256,
            return_dict_in_generate=True,
            output_scores=True,
            num_beams=1,
        )

        text = self.tokenizer.batch_decode(
            output.sequences,
            skip_special_tokens=True,
        )[0].strip()

        probabilities = []
        for score in output.scores:
            probabilities.append(torch.softmax(score, dim=-1).max(dim=-1).values)

        confidence = (
            torch.stack(probabilities).mean().item() * 100.0
            if probabilities else 0.0
        )

        return RecognitionResult(
            text=text,
            confidence=round(confidence, 2),
            metadata={
                "model_type": self.model.config.model_type,
                "device": str(self.device),
                "generated_tokens": int(output.sequences.shape[-1]),
            },
        )
