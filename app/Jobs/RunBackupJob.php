<?php

namespace App\Jobs;

use App\Models\BackupLog;
use App\Notifications\BackupFailedNotification;
use App\Notifications\BackupSuccessNotification;
use App\Services\BackupService;
use App\Services\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Queued job that orchestrates the full backup pipeline:
 * 1. Dump database / zip files
 * 2. Encrypt the archive
 * 3. Upload to Google Drive
 * 4. Verify integrity
 * 5. Save metadata
 * 6. Send notifications
 * 7. Clean up temp files
 */
class RunBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Backoff intervals in seconds between retries.
     */
    public array $backoff = [30, 60, 120];

    /**
     * Maximum execution time in seconds (10 minutes for large DBs).
     */
    public int $timeout = 600;

    /**
     * @param string $type     Backup type: 'full', 'database', or 'files'
     * @param int|null $triggeredBy  User ID who triggered, null for scheduler
     */
    public function __construct(
        protected string $type = 'full',
        protected ?int $triggeredBy = null,
    ) {}

    /**
     * Execute the backup pipeline.
     */
    public function handle(BackupService $backupService, GoogleDriveService $driveService): void
    {
        $startTime = microtime(true);

        Log::info('[RunBackupJob] Starting backup', [
            'type' => $this->type,
            'triggered_by' => $this->triggeredBy ?? 'scheduler',
        ]);

        try {
            // Step 1: Create backup archive based on type
            $backupFile = match ($this->type) {
                'database' => $backupService->createDatabaseBackup(),
                'files' => $backupService->createFilesBackup(),
                default => $backupService->createFullBackup(),
            };

            $originalSize = filesize($backupFile);
            $originalFilename = basename($backupFile);

            // Step 2: Generate checksum before encryption
            $checksum = $backupService->generateChecksum($backupFile);

            // Step 3: Encrypt if enabled
            $isEncrypted = false;
            if (config('cuanflow-backup.encryption.enabled') && config('cuanflow-backup.encryption.key')) {
                $backupFile = $backupService->encryptFile($backupFile);
                $isEncrypted = true;
            }

            $finalFilename = basename($backupFile);
            $finalSize = filesize($backupFile);

            // Step 4: Upload to Google Drive
            $googleDriveFileId = null;
            $disk = 'local';

            if ($driveService->isConfigured()) {
                $googleDriveFileId = $driveService->upload($backupFile, $finalFilename);
                $disk = 'google';
            } else {
                Log::warning('[RunBackupJob] Google Drive not configured, keeping backup locally');
            }

            // Step 5: Determine backup type for database record
            $dbType = $disk === 'google'
                ? "gdrive_{$this->type}"
                : $this->type;

            // Step 6: Save metadata to database
            $backupLog = BackupLog::create([
                'filename' => $finalFilename,
                'disk' => $disk,
                'path' => $disk === 'google' ? $finalFilename : $backupFile,
                'size' => $finalSize,
                'type' => $dbType,
                'status' => 'completed',
                'error_message' => null,
                'google_drive_file_id' => $googleDriveFileId,
                'checksum' => $checksum,
                'is_encrypted' => $isEncrypted,
                'created_by' => $this->triggeredBy,
            ]);

            $duration = round(microtime(true) - $startTime, 2);

            Log::info('[RunBackupJob] Backup completed successfully', [
                'backup_id' => $backupLog->id,
                'filename' => $finalFilename,
                'size' => $finalSize,
                'disk' => $disk,
                'encrypted' => $isEncrypted,
                'duration_seconds' => $duration,
            ]);

            // Step 7: Clean up local temp file (if uploaded to Drive)
            if ($disk === 'google' && file_exists($backupFile)) {
                unlink($backupFile);
            }

            // Step 8: Send success notification
            $this->sendSuccessNotification($backupLog, $duration);

        } catch (\Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);

            Log::error('[RunBackupJob] Backup failed', [
                'type' => $this->type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'duration_seconds' => $duration,
            ]);

            // Save failure record
            BackupLog::create([
                'filename' => $originalFilename ?? 'unknown',
                'disk' => 'local',
                'path' => '',
                'size' => 0,
                'type' => $this->type,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'is_encrypted' => false,
                'created_by' => $this->triggeredBy,
            ]);

            // Clean up temp files
            $backupService->cleanupTempFiles();

            // Send failure notification
            $this->sendFailureNotification($e->getMessage());

            throw $e;
        }
    }

    /**
     * Send backup success notification.
     */
    protected function sendSuccessNotification(BackupLog $backup, float $duration): void
    {
        $notifyEmail = config('cuanflow-backup.notify_email');

        if ($notifyEmail) {
            try {
                Notification::route('mail', $notifyEmail)
                    ->notify(new BackupSuccessNotification($backup, $duration));
            } catch (\Exception $e) {
                Log::warning('[RunBackupJob] Failed to send success notification', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send backup failure notification.
     */
    protected function sendFailureNotification(string $errorMessage): void
    {
        $notifyEmail = config('cuanflow-backup.notify_email');

        if ($notifyEmail) {
            try {
                Notification::route('mail', $notifyEmail)
                    ->notify(new BackupFailedNotification($this->type, $errorMessage));
            } catch (\Exception $e) {
                Log::warning('[RunBackupJob] Failed to send failure notification', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
