<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Voice Processor Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Python-based voice verification engine.
    | Now runs as a FastAPI Microservice for performance.
    |
    */

    'api_url' => env('VOICE_API_URL', 'http://127.0.0.1:8000'),

    'ffmpeg_path' => env('FFMPEG_PATH', 'ffmpeg'),

    'log_channel' => 'voice_verification',

    'timeout' => env('VOICE_TIMEOUT', 60), // seconds
];
