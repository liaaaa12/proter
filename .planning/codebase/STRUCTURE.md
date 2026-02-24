# Directory Structure

## Key Directories

- `app/`: Core PHP logic.
    - `Http/Controllers/`: Request handlers.
    - `Models/`: Eloquent models (Goal, User).
- `resources/js/`: React frontend.
    - `Pages/`: Page components.
    - `Components/`: Reusable UI elements.
    - `Hooks/`: React custom hooks (e.g., `useVoiceRecording.js`).
- `database/`: Database schema and seeding.
- `scripts/`: Python processing scripts.
- `public/`: Publicly accessible assets.
- `tests/`: Feature and Unit tests.
- `config/`: Application configuration files.

## Project Root

- `composer.json` / `package.json`: Dependency manifests.
- `vite.config.js`: Asset bundling configuration.
- `.env`: Environment variables.
- `artisan`: Laravel CLI tool.
