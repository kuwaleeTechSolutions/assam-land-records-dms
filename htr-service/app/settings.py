from pydantic_settings import BaseSettings, SettingsConfigDict

class Settings(BaseSettings):
    model_path: str = "/models/assamese-land-record-htr"
    api_token: str = ""
    device: str = "cpu"
    max_image_mb: int = 25
    min_confidence: float = 0.0
    model_config = SettingsConfigDict(env_prefix="HTR_", env_file=".env", extra="ignore")

settings = Settings()
