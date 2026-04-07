# Codebase Structure

## Directory Layout
- `/app/` - Laravel App layer containing Models and Controllers. Currently, VoiceTransactionController and OcrAiController house most business logic.
- `/config/` - Laravel configs.
- `/database/` - Migrations and SQLite database references.
- `/resources/js/` - React application root.
  - `/Pages/` - Main views mapped to Inertia routes (Dashboard.jsx, AuthLayout.jsx, Laporan.jsx, etc).
  - `/Components/` - Reusable UI widgets.
  - `/Hooks/` - Custom React hooks (`useVoiceCommand.js`, `useAudioRecorder.js`).
- `/scripts/` - All Python services and models.
  - `api_server.py` - FastAPI entrypoint.
  - `voice_processor_ecapa.py` - Core AI logic wrapper.
  - `start-fastapi.bat` - Execution startup script snippet.
- `/.planning/` - Project documentation and orchestration state artifacts.

## Observation
- Currently, component rendering files (e.g. `AuthLayout.jsx`) and specific controller implementations are large and encapsulate multiple responsibilities.
