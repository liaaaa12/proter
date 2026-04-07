# Project proter: Grand Refactoring & Clean Architecture

## Core Value
A highly maintainable, modular, and organized codebase for Voica (Proter) spanning across Laravel backend APIs, React/Inertia Frontend, and FastAPI Python microservices. The focus is to eliminate monolithic files, extract business logic into services, reorganize directory structures logically, and adhere to SOLID principles.

## Context
"proter" has grown into a functional voice-driven financial app with a complex tech stack (Laravel + React + Python AI). However, rapid feature development has led to significant technical debt: bloated React components (e.g., `AuthLayout.jsx`), heavy "fat-controllers" in Laravel (`VoiceTransactionController`), and lack of component/service grouping. The goal of this milestone is a comprehensive cleanup of both Frontend and Backend without breaking existing functionality.

## Requirements

### Validated
- ✓ **Voice STT Microservice** — Faster Whisper running fully offline on port 8000
- ✓ **Inertia SPA Flow** — React routing via Laravel
- ✓ **Core Operations** — Dashboard, Budgeting, Laporan working seamlessly

### Active (Hypotheses)
- [ ] **Frontend API Organization:** Create a dedicated `api/` folder in the frontend (e.g., `resources/js/api/`) to centralize all Axios or Fetch API calls instead of having them scattered in hooks or components.
- [ ] **Frontend Component Separation:** Break down `AuthLayout.jsx` and page files into logical `UI/`, `Layouts/`, and feature-specific component folders.
- [ ] **Backend Service Layer Extraction:** Move complex logic from Laravel API Controllers into dedicated `App\Services` classes.
- [ ] **Hooks Organization:** Clean up React Custom Hooks (`useVoiceCommand`, `useAudioRecorder`) and ensure bulletproof strictness.
- [ ] **Directory Restructuring:** Establish clean folder structures for both React resources (`resources/js`) and Python script orchestrators (`scripts/`).
- [ ] **Code Cleansing:** Remove obsolete/unused dead code and deprecated libraries globally.

### Out of Scope
- Adding new heavy user-facing features (Focus is strictly on code quality and structure).
- Modifying AI training models (ECAPA/Faster Whisper inference logic works well).

## Key Decisions
| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Full-Stack Clean Up | Both FE and BE suffer from bloating, so resolving one half will not resolve tech debt holistically. | Pending |

---
*Last updated: 2026-04-06 after initialization*
