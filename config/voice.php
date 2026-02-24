<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Voice Processor Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Python-based voice verification engine.
    |
    */

    'python_path' => env('VOICE_PYTHON_PATH', base_path('.venv/Scripts/python.exe')),

    'script_path' => base_path('scripts/voice_processor_ecapa.py'),

    'log_channel' => 'voice_verification',

    'timeout' => 30, // seconds
];
