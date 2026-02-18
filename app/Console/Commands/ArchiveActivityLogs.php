<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\BackupLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ArchiveActivityLogs extends Command
{
    protected $signature = 'log:archive {--days=30 : Archive logs older than this many days}';

    protected $description = 'Archive old activity logs to a JSON file and register in backup_logs';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $query = Activity::where('created_at', '<', $cutoff);
        $count = $query->count();

        if ($count === 0) {
            $this->info('No activity logs older than '.$days.' days to archive.');

            return self::SUCCESS;
        }

        $this->info("Found {$count} activity log(s) older than {$days} days.");

        // Prepare the export
        $timestamp = now()->format('Y-m-d_His');
        $filename = "activity_logs_archive_{$timestamp}.json";
        $directory = 'backups/logs';
        $path = "{$directory}/{$filename}";

        try {
            // Ensure directory exists
            Storage::makeDirectory($directory);

            // Export in chunks to avoid memory issues
            $tempFile = tempnam(sys_get_temp_dir(), 'activity_log_');
            $handle = fopen($tempFile, 'w');
            fwrite($handle, '[');

            $first = true;
            $query->orderBy('id')->chunk(500, function ($logs) use ($handle, &$first) {
                foreach ($logs as $log) {
                    if (! $first) {
                        fwrite($handle, ',');
                    }
                    fwrite($handle, json_encode($log->toArray(), JSON_PRETTY_PRINT));
                    $first = false;
                }
            });

            fwrite($handle, ']');
            fclose($handle);

            // Move to storage
            $fileSize = filesize($tempFile);
            Storage::put($path, file_get_contents($tempFile));
            unlink($tempFile);

            $this->info("Exported to storage/{$path} ({$this->humanFileSize($fileSize)})");

            // Register in backup_logs
            $backup = BackupLog::create([
                'filename' => $filename,
                'disk' => 'local',
                'path' => $path,
                'size' => $fileSize,
                'type' => 'database',
                'status' => 'completed',
                'created_by' => null,
            ]);

            $this->info("Backup log entry created (ID: {$backup->id})");

            // Now safely delete the archived records
            $deleted = Activity::where('created_at', '<', $cutoff)->delete();
            $this->info("Deleted {$deleted} archived activity log(s) from database.");

            $this->newLine();
            $this->info('✅ Activity log archive completed successfully!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            // Record failure but do NOT delete
            BackupLog::create([
                'filename' => $filename ?? 'failed_archive',
                'disk' => 'local',
                'path' => $path ?? '',
                'size' => 0,
                'type' => 'database',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'created_by' => null,
            ]);

            $this->error('❌ Archive failed: '.$e->getMessage());
            $this->error('Original logs were NOT deleted.');

            return self::FAILURE;
        }
    }

    private function humanFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
