<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException; // Added
use Illuminate\Support\Facades\Log;
use App\DTOs\VoiceVerificationResult; // Added

class VoiceProcessorService
{
    /**
     * Execute a voice processing command.
     *
     * @param string $command 'enroll' or 'verify'
     * @param array $args Arguments for the command
     * @return \App\DTOs\VoiceVerificationResult
     */
    public function execute(string $command, array $args): \App\DTOs\VoiceVerificationResult
    {
        $python = config('voice.python_path');
        $script = config('voice.script_path');

        $processArgs = array_merge([$python, $script, $command], $args);

        // Ensure crucial Windows environment variables are passed to the sub-process
        // This fixes WinError 10106 (Winsock initialization failure) on some Windows systems.
        $env = [];
        foreach (['SystemRoot', 'SystemDrive', 'PATH', 'TEMP', 'TMP'] as $var) {
            if ($value = getenv($var)) {
                $env[$var] = $value;
            }
        }

        $process = new Process($processArgs, null, $env);
        $process->setTimeout(config('voice.timeout'));

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                $error = substr($process->getErrorOutput(), 0, 500);
                Log::channel(config('voice.log_channel'))->error("Voice Process failed: " . $error);
                return \App\DTOs\VoiceVerificationResult::failure("Process failed: " . $error);
            }

            $output = $process->getOutput();

            // Handle output that might contain debug info or warnings before JSON
            $jsonStart = strpos($output, '{');
            if ($jsonStart !== false) {
                $output = substr($output, $jsonStart);
            }

            $result = json_decode($output, true);

            if (!$result) {
                Log::channel(config('voice.log_channel'))->error("Voice Process output invalid JSON: " . substr($output, 0, 200));
                return \App\DTOs\VoiceVerificationResult::failure("Invalid JSON output from script");
            }

            return \App\DTOs\VoiceVerificationResult::fromArray($result);
        } catch (ProcessTimedOutException $e) {
            Log::channel(config('voice.log_channel'))->error("Voice Process timeout: " . $e->getMessage());
            return \App\DTOs\VoiceVerificationResult::failure("Execution timed out after " . config('voice.timeout') . " seconds");
        } catch (\Exception $e) {
            Log::channel(config('voice.log_channel'))->error("Voice Process error: " . $e->getMessage());
            return \App\DTOs\VoiceVerificationResult::failure($e->getMessage());
        }
    }

    /**
     * Log messages to the dedicated voice log channel.
     */
    protected function log(string $message, string $level = 'info'): void
    {
        Log::channel(config('voice.log_channel', 'stack'))->$level("[VoiceProcessor] $message");
    }
}
