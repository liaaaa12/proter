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
     * Convert audio format using FFmpeg (if available)
     * Falls back to original format if FFmpeg not available
     */
    public function convertToWav(string $inputPath): string
{
    $ffmpegPath = base_path('ffmpeg/bin/ffmpeg.exe');

    if (!file_exists($ffmpegPath)) {
        \Log::error('FFmpeg not found at: ' . $ffmpegPath);
        return $inputPath;
    }

    $outputPath = pathinfo($inputPath, PATHINFO_DIRNAME) . '/' .
                  pathinfo($inputPath, PATHINFO_FILENAME) . '.wav';

    // Pakai exec agar FFmpeg benar-benar selesai
    $command = "\"$ffmpegPath\" -i \"$inputPath\" -ar 16000 -ac 1 \"$outputPath\" 2>&1";

    exec($command, $output, $status);

    \Log::info("FFmpeg status: $status");
    \Log::info("FFmpeg output: " . implode("\n", $output));

    if ($status === 0 && file_exists($outputPath) && filesize($outputPath) > 0) {

        \Log::info("UNLINK TRY: $inputPath");

        if (!unlink($inputPath)) {
            \Log::error("UNLINK FAILED: $inputPath");
        } else {
            \Log::info("UNLINK SUCCESS: $inputPath");
        }

        return $outputPath;
    }

    \Log::error("Konversi gagal, WAV tidak dibuat");
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
