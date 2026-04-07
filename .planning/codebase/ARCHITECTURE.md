# System Architecture

## Overall Pattern
- **MVC (Laravel)**: Standard Model-View-Controller structure as the primary web backend.
- **Frontend SPA**: React components served via Inertia.js to achieve Single Page Application performance without REST API overhead.
- **Microservice Architecture**: Python AI processing is spun off as a separate microservice via FastAPI.

## Voice Command Data Flow
1. **Frontend**: React components (`useAudioRecorder.js` and `useVoiceCommand.js`) capture voice using `MediaRecorder`.
2. **Action Trigger**: The stopping of the media recorder triggers the internal `onStop` callback directly processing the audio without re-rendering cycles.
3. **Voice to Text**: The frontend forwards the raw `.webm` blob directly to the Python FastAPI microservice (`/transcribe-upload` endpoint) holding models in RAM.
4. **Backend Transcription**: FastAPI uses `ffmpeg` to securely convert audio into PCM `.wav` format, and transcribes using `Faster Whisper` in offline mode.
5. **Local Validation**: Recognized intents route actions to React inertia paths natively (like `/dashboard`) OR trigger Laravel endpoint (`/api/parse-voice-text`) to interpret transaction amounts/entities via NLP heuristics.
6. **Persistence**: Laravel processes and stores transactions natively utilizing models.
