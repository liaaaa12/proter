<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if tanggal column doesn't exist
        if (!Schema::hasColumn('transaction', 'tanggal')) {
            Schema::table('transaction', function (Blueprint $table) {
                // Add tanggal column
                $table->date('tanggal')->nullable()->after('id');
            });
            
            // Update existing records: set tanggal from created_at
            DB::statement('UPDATE transaction SET tanggal = DATE(created_at) WHERE tanggal IS NULL');
            
            // Make column non-nullable after updating
            Schema::table('transaction', function (Blueprint $table) {
                $table->date('tanggal')->nullable(false)->change();
            });
        }
        
        // Check if user_id column doesn't exist (just in case)
        if (!Schema::hasColumn('transaction', 'user_id')) {
            Schema::table('transaction', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
            
            // Update existing records
            $firstUserId = DB::table('users')->first()->id ?? 1;
            DB::statement("UPDATE transaction SET user_id = {$firstUserId} WHERE user_id IS NULL");
            
            Schema::table('transaction', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            if (Schema::hasColumn('transaction', 'tanggal')) {
                $table->dropColumn('tanggal');
            }
            // Don't drop user_id as it might be used elsewhere
        });
    }
};
