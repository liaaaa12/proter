# Tech Stack

## Core Technologies
- **Backend:** Laravel (PHP)
- **Frontend SPA:** React with Inertia.js
- **Styling:** Tailwind CSS + Heroicons (Solid style)
- **AI/Voice Service:** Python via FastAPI (served locally on port 8000)

## AI / Machine Learning Models
- **Speaker Verification:** ECAPA-TDNN (for voice enrollment and matching)
- **Anti-Spoofing:** AASIST (for detecting spoofed/synthetic audio)
- **Speech-to-Text (STT):** Faster Whisper (`small` model with `int8` quantization)

## System Dependencies
- **FFmpeg:** Required on the host system to process audio sent from browser (WebM/OGG to WAV PCM 16kHz) as required by STT engines.

## Database
- SQLite (based on `.env` configuration)
