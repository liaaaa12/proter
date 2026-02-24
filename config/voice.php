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

    'python_path' => env('PYTHON_EXEC', base_path('.venv/Scripts/python.exe')),

    'script_path' => base_path('scripts/voice_processor_ecapa.py'),

    'ffmpeg_path' => env('FFMPEG_PATH', 'ffmpeg'),

    'log_channel' => 'voice_verification',

    'timeout' => 30, // seconds
];
