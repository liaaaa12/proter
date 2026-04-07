# Concerns & Tech Debt

## Areas of Technical Debt
1. **Bloated React Components:** Files like `AuthLayout.jsx` and `Dashboard.jsx` exceed acceptable bounds of complexity (often carrying navigation state, layout presentation, routing, and specific ML-driven modals).
2. **Coupled Controllers:** Laravel API endpoints have heavy string manipulations inside controllers (`VoiceTransactionController`, `OcrAiController`). These should ideally be abstracted into native service classes.
3. **Implicit Folder Structure:** `resources/js/Pages/` acts as a dump point for large views rather than a clean sub-directory structure.

## Technical Bottleneck Risks
1. **Frontend Context Propagation:** Poor context scoping historically led to poison loops (`useEffect` loops dealing with `MediaRecorder` hook blobs). Strong callback patterns exist now but need monitoring if additional voice features are added.
2. **Python Temporary File I/O:** AI inferences write temporarily to physical disks via `tempfile` handling WebM payload to WAV conversion before processing into RAM prediction. If crash events occur mid-request, orphan blobs could fill temp allocations.

## Next Steps Recommended (Refactoring Goal)
- Extract modals and feature-specific sidebars from `AuthLayout.jsx` into smaller pure components.
- Move business logic from Laravel API endpoints into distinct Service or Repository patterns.
- Implement explicit try-finally robust GC wrappers for file handling in Python microservice endpoints.
