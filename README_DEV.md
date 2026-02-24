# Developer Guide: Proter Voice Verification System

This document provides a technical overview and setup instructions for the refactored voice verification system.

## 🏗️ Architecture Overview

The system uses a **Standardized Bridge** between Laravel (PHP) and a Python-powered voice processing engine.

### Data Flow

1. **Frontend**: Captures audio and sends base64/file to Laravel.
2. **Laravel Controller**: Injects domain services.
3. **Domain Services**:
    - `AudioProcessingService`: Decodes base64 and normalizes audio to 16kHz Mono WAV via FFmpeg.
    - `VoiceEnrollmentService`: Orchestrates the pendaftaran (enrollment) flow.
    - `VoiceVerificationService`: Orchestrates the verification flow.
4. **Subsystem Bridge**: `VoiceProcessorService` executes Python scripts via Symfony Process, passing arguments and environment variables.
5. **Python Engine**:
    - `AASIST`: Layer 1/2 for Anti-Spoofing.
    - `ECAPA-TDNN`: Layer 2/3 for Speaker Verification (Speaker Embeddings).
    - `SpeechBrain/STT`: Layer 3 for Challenge Validation.
6. **Result**: Python outputs JSON, which is mapped to a typed `VoiceVerificationResult` DTO in PHP.

## 🛠️ Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer
- Python 3.10 - 3.13
- FFmpeg (must be in system PATH)

### Python Environment

1. Navigate to the project root.
2. Run the setup script:
    ```powershell
    ./scripts/setup.ps1
    ```
    _This creates a `.venv`, upgrades pip, and installs requirements from `requirements.txt`._

### Configuration

Verify your `config/voice.php`:

```php
'python_path' => base_path('.venv/Scripts/python.exe'),
'script_path' => base_path('scripts/voice_processor_ecapa.py'),
```

## 🧪 Testing

Run unit and integration tests:

```bash
php artisan test
```

Integration tests require the Python environment to be correctly setup as they run the actual scripts.

## 📝 Key Files

- **Bridge**: `app/Services/VoiceProcessorService.php`
- **DTO**: `app/DTOs/VoiceVerificationResult.php`
- **Engine**: `scripts/voice_processor_ecapa.py`
- **Anti-Spoofing**: `scripts/anti_spoofing.py`

## 🛡️ Security Levels

- **Standard**: Password only.
- **2-Layer Secure**: Anti-Spoofing + Voice Match.
- **3-Layer Secure**: Text Challenge + Anti-Spoofing + Voice Match.
