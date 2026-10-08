<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const LOG_TIMEZONE = 'Asia/Jakarta';

    /**
     * Store everything the voice_verification log file records, so attempts can be analysed
     * in the database (e.g. with DBeaver), and keep their times in WIB:
     *
     * 1. add the columns the file log has but the table lacked
     * 2. fill them for existing rows from storage/logs/voice_verification-*.log
     * 3. make created_at/updated_at DATETIME, so every client shows the stored WIB wall-clock
     *    time as is (a TIMESTAMP is converted to each session's time zone, e.g. over remote)
     * 4. shift existing rows, written in the app timezone (UTC), to WIB
     */
    public function up(): void
    {
        Schema::table('voice_verification_logs', function (Blueprint $table) {
            // Layer 1 (Voice Lock challenge): STT
            $table->text('stt_transcript')->nullable()->after('is_match');
            $table->text('stt_expected')->nullable()->after('stt_transcript');
            $table->decimal('stt_similarity', 5, 2)->nullable()->after('stt_expected');

            // AASIST
            $table->decimal('aasist_spoof', 5, 2)->nullable()->after('aasist_bonafide');
            $table->string('aasist_security_level', 20)->nullable()->after('aasist_spoof'); // standard, elevated, blocked
            $table->string('aasist_model', 64)->nullable()->after('aasist_security_level');  // weights file, e.g. AASIST_voica.pth

            // Rejection and request context
            $table->unsignedTinyInteger('rejected_layer')->nullable()->after('rejected_reason');
            $table->string('user_agent', 512)->nullable()->after('ip_address');

            $table->index('aasist_security_level');
        });

        $this->backfillFromLogFiles();

        Schema::table('voice_verification_logs', function (Blueprint $table) {
            $table->dateTime('created_at')->nullable()->change();
            $table->dateTime('updated_at')->nullable()->change();
        });

        $this->shiftTimes($this->offsetMinutes(config('app.timezone'), self::LOG_TIMEZONE));
    }

    public function down(): void
    {
        $this->shiftTimes(-$this->offsetMinutes(config('app.timezone'), self::LOG_TIMEZONE));

        Schema::table('voice_verification_logs', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->change();
            $table->timestamp('updated_at')->nullable()->change();
            $table->dropIndex(['aasist_security_level']);
            $table->dropColumn([
                'stt_transcript', 'stt_expected', 'stt_similarity',
                'aasist_spoof', 'aasist_security_level', 'aasist_model',
                'rejected_layer', 'user_agent',
            ]);
        });
    }

    /**
     * Rows and file entries were written at the same moment by VoiceVerificationLogger, so an
     * entry belongs to the row created within 2 s of its timestamp with the same AASIST score.
     * The account tried at login and the AASIST weights were never logged, so they stay NULL.
     */
    private function backfillFromLogFiles(): void
    {
        foreach (glob(storage_path('logs/voice_verification-*.log')) ?: [] as $file) {
            foreach (file($file, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
                if (!preg_match('/Voice verification (?:SUCCESS|FAILED) (\{.*\})/', $line, $m)) {
                    continue;
                }
                $entry = json_decode($m[1], true);
                if (!is_array($entry) || empty($entry['timestamp'])) {
                    continue;
                }

                $at = Carbon::parse($entry['timestamp']);
                $bonafide = $entry['aasist_bonafide'] ?? null;

                DB::table('voice_verification_logs')
                    ->whereBetween('created_at', [$at->toDateTimeString(), $at->copy()->addSeconds(2)->toDateTimeString()])
                    ->when($bonafide === null,
                        fn ($q) => $q->whereNull('aasist_bonafide'),
                        fn ($q) => $q->where('aasist_bonafide', $bonafide))
                    ->whereNull('aasist_security_level')
                    ->whereNull('user_agent')
                    ->orderBy('id')
                    ->limit(1)
                    ->update([
                        'stt_transcript' => $entry['stt_transcript'] ?? null,
                        'stt_expected' => $entry['stt_expected'] ?? null,
                        'stt_similarity' => $entry['stt_similarity'] ?? null,
                        'aasist_spoof' => $entry['aasist_spoof'] ?? null,
                        'aasist_security_level' => $entry['aasist_security_level'] ?? null,
                        'rejected_layer' => $entry['rejected_layer'] ?? null,
                        'user_agent' => isset($entry['user_agent']) ? mb_substr($entry['user_agent'], 0, 512) : null,
                    ]);
            }
        }
    }

    private function offsetMinutes(string $from, string $to): int
    {
        return (int) (Carbon::now($to)->utcOffset() - Carbon::now($from)->utcOffset());
    }

    private function shiftTimes(int $minutes): void
    {
        if ($minutes === 0) {
            return;
        }
        DB::table('voice_verification_logs')->update([
            'created_at' => DB::raw("created_at + INTERVAL {$minutes} MINUTE"),
            'updated_at' => DB::raw("updated_at + INTERVAL {$minutes} MINUTE"),
        ]);
    }
};
