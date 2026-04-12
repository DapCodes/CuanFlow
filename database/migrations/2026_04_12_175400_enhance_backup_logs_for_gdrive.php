<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Enhance backup_logs table with Google Drive support,
     * encryption tracking, and integrity verification.
     */
    public function up(): void
    {
        // Extend the type enum to include gdrive variants
        DB::statement("ALTER TABLE backup_logs MODIFY COLUMN type ENUM('full', 'database', 'files', 'activity_log', 'gdrive_full', 'gdrive_database', 'gdrive_files')");

        Schema::table('backup_logs', function (Blueprint $table) {
            $table->string('google_drive_file_id')->nullable()->after('error_message');
            $table->string('checksum')->nullable()->after('google_drive_file_id');
            $table->boolean('is_encrypted')->default(false)->after('checksum');
            $table->index(['status', 'created_at'], 'backup_logs_status_created_idx');
            $table->index('type', 'backup_logs_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backup_logs', function (Blueprint $table) {
            $table->dropIndex('backup_logs_status_created_idx');
            $table->dropIndex('backup_logs_type_idx');
            $table->dropColumn(['google_drive_file_id', 'checksum', 'is_encrypted']);
        });

        DB::statement("ALTER TABLE backup_logs MODIFY COLUMN type ENUM('full', 'database', 'files', 'activity_log')");
    }
};
