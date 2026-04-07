# Integrations

## FFmpeg
- Used heavily as a system binary dependency `ffmpeg` inside `scripts/api_server.py`. Converts native browser audio formats (WebM/OGG) to strictly PCM 16kHz WAV format for model prediction ingestion.

## Local Services
- **Laravel Artisans:** Backend operates through the local web server provided by `php artisan serve`.
- **FastAPI Uvicorn Layer:** Microservices operate locally on port `8000`. Fast AI predictions function strictly independent of cloud providers (no external AWS/GCP API hooks).

## Package Integrations
- Composer dependencies handling backend libraries.
- Node.js (`npm`) managing React + Vite asset bundling.
