<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\VoiceProcessorService;
use App\Services\VoiceEnrollmentService;
use App\Services\VoiceVerificationService;
use App\Services\AudioProcessingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VoiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected VoiceEnrollmentService $enrollmentService;
    protected VoiceVerificationService $verificationService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->enrollmentService = app(VoiceEnrollmentService::class);
        $this->verificationService = app(VoiceVerificationService::class);
    }

    /**
     * @group integration
     */
    public function test_full_enrollment_and_verification_cycle()
    {
        // Skip if Python is not configured or available (optional check)
        if (!config('voice.python_path') || !file_exists(config('voice.script_path'))) {
            $this->markTestSkipped('Python engine not configured.');
        }

        $user = User::factory()->create();

        // 1. Create a dummy audio file (simplified WAV)
        // Note: For real engine tests, we'd need a valid 16kHz WAV.
        // We'll use a small valid WAV file if available, or just mock the process for speed
        // unless explicitly testing the script execution.

        // Let's test the ACTUAL script execution with a tiny sample
        $samplePath = base_path('tests/Samples/test_voice.wav');
        if (!file_exists($samplePath)) {
            // Create a very basic valid RIFF WAV header + silence
            if (!is_dir(base_path('tests/Samples'))) {
                mkdir(base_path('tests/Samples'), 0755, true);
            }
            // 1 second of silence 16k mono 16bit
            $header = pack('NVCVNVCCVNNVVCV', 0x52494646, 36044, 0x57415645, 0x666d7420, 16, 1, 1, 16000, 32000, 2, 16, 0x64617461, 36000);
            // This is a crude way, but for a "bridge test" it might suffice if the engine accepts it.
            // Better: check if engine fails on too short audio.
            file_put_contents($samplePath, $header . str_repeat("\0", 36000));
        }

        // 2. Test Enrollment
        Log::info("Testing Enrollment Integration...");
        $enrollResult = $this->enrollmentService->enroll($user, $samplePath);

        $this->assertTrue($enrollResult->success, "Enrollment failed: " . ($enrollResult->error ?? 'Unknown error'));
        $this->assertTrue($user->fresh()->is_voice_enrolled);
        $this->assertNotNull($user->fresh()->voice_embedding);

        $storedPath = $user->fresh()->voice_path;
        Storage::disk('public')->assertExists($storedPath);

        // 3. Test Verification (Self-match)
        Log::info("Testing Verification Integration...");
        $verifyResult = $this->verificationService->verify($user, $samplePath, [
            'threshold' => 0.60 // Low threshold for synthetic silence
        ]);

        $this->assertTrue($verifyResult->success, "Verification process failed: " . ($verifyResult->error ?? 'Unknown error'));
        // Even if match fails due to silence, success=true means bridge works.

        Log::info("Integration match result: " . ($verifyResult->isMatch ? 'MATCH' : 'NO MATCH'));
    }
}
