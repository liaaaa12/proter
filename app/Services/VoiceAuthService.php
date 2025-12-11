<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class VoiceAuthService
{
    /**
     * Convert base64 audio to file
     */
    public function base64ToFile(string $base64Data): UploadedFile
    {
        // Extract the base64 encoded binary data
        if (preg_match('/^data:audio\/(\w+);base64,/', $base64Data, $type)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]); // Get file extension
        } else {
            throw new \Exception('Invalid base64 audio data');
        }

        // Decode base64
        $audioData = base64_decode($base64Data);
        
        if ($audioData === false) {
            throw new \Exception('Failed to decode base64 data');
        }

        // Create temporary file
        $tempFilePath = sys_get_temp_dir() . '/' . uniqid('voice_', true) . '.' . $type;
        file_put_contents($tempFilePath, $audioData);

        // Create UploadedFile instance
        return new UploadedFile(
            $tempFilePath,
            'voice.' . $type,
            'audio/' . $type,
            null,
            true // Mark as test file
        );
    }

    /**
     * Verify audio format is WAV (browser-side conversion)
     * No server-side conversion needed - browser already converts to WAV 16kHz mono
     * This function now only validates the file format
     */
    public function convertToWav(string $inputPath): string
    {
        // Check if file is already WAV format
        $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));
        
        if ($extension === 'wav') {
            \Log::info('✅ File is already WAV format (browser-side conversion): ' . $inputPath);
            return $inputPath;
        }

        // If file is not WAV, log warning but still return it
        // This should not happen if browser-side conversion is working correctly
        \Log::warning('⚠️ File is not WAV format: ' . $extension . ' - Browser conversion may have failed');
        \Log::warning('File path: ' . $inputPath);
        
        // Return original file - Python script will attempt to process it
        return $inputPath;
    }


    /**
     * Clean up temporary files
     */
    public function cleanup(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
