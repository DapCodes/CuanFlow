<?php

namespace App\Console\Commands;

use App\Models\BackupLog;
use App\Services\GoogleDriveService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Artisan command to clean up old backups based on retention policy.
 *
 * Retention policy:
 *   - Keep last N daily backups (default: 7)
 *   - Keep last N weekly backups (default: 4)
 *   - Delete everything older
 *
 * Usage:
 *   php artisan backup:clean
 *   php artisan backup:clean --dry-run    # Preview without deleting
 */
class BackupCleanCommand extends Command
{
    protected $signature = 'backup:clean
                            {--dry-run : Preview what would be deleted without actually deleting}';

    protected $description = 'Clean up old backups based on retention policy';

    public function handle(GoogleDriveService $driveService): int
    {
        $dryRun = $this->option('dry-run');
        $retention = config('cuanflow-backup.retention');

        $dailyKeep = $retention['daily_backups'] ?? 7;
        $weeklyKeep = $retention['weekly_backups'] ?? 4;

        $this->info('🧹 Starting backup cleanup...');
        $this->info("  Retention: {$dailyKeep} daily, {$weeklyKeep} weekly");

        if ($dryRun) {
            $this->warn('  DRY RUN — no files will be deleted');
        }

        // Get all successful backups ordered by creation date (newest first)
        $backups = BackupLog::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($backups->isEmpty()) {
            $this->info('📭 No backups found. Nothing to clean.');
            return self::SUCCESS;
        }

        $toKeep = collect();
        $now = Carbon::now();

        // Phase 1: Keep the latest N daily backups
        $dailyCutoff = $now->copy()->subDays($dailyKeep);
        $dailyBackups = $backups->filter(fn ($b) => $b->created_at->greaterThanOrEqualTo($dailyCutoff));
        $toKeep = $toKeep->merge($dailyBackups->pluck('id'));

        // Phase 2: From remaining older backups, keep one per week for N weeks
        $weeklyStart = $dailyCutoff->copy();
        $weeklyCutoff = $weeklyStart->copy()->subWeeks($weeklyKeep);

        $olderBackups = $backups->filter(
            fn ($b) => $b->created_at->lessThan($dailyCutoff) && $b->created_at->greaterThanOrEqualTo($weeklyCutoff)
        );

        // Group by week number and keep the most recent one per week
        $weeklyGrouped = $olderBackups->groupBy(fn ($b) => $b->created_at->format('Y-W'));
        foreach ($weeklyGrouped as $weekBackups) {
            $newest = $weekBackups->sortByDesc('created_at')->first();
            if ($newest) {
                $toKeep->push($newest->id);
            }
        }

        // Always keep the newest backup regardless
        if ($backups->first()) {
            $toKeep->push($backups->first()->id);
        }

        $toKeep = $toKeep->unique();

        // Determine which backups to delete
        $toDelete = $backups->filter(fn ($b) => !$toKeep->contains($b->id));

        // Also include failed backups older than daily retention
        $failedToDelete = BackupLog::where('status', 'failed')
            ->where('created_at', '<', $dailyCutoff)
            ->get();

        $allToDelete = $toDelete->merge($failedToDelete);

        if ($allToDelete->isEmpty()) {
            $this->info('✨ All backups are within retention policy. Nothing to clean.');
            return self::SUCCESS;
        }

        $this->info("  Total backups: {$backups->count()}");
        $this->info("  Keeping: {$toKeep->count()}");
        $this->info("  Deleting: {$allToDelete->count()}");

        $deletedCount = 0;
        $freedBytes = 0;

        foreach ($allToDelete as $backup) {
            $this->line("  🗑  {$backup->filename} ({$backup->getSizeForHumans()}) - {$backup->created_at->format('Y-m-d H:i')}");

            if (!$dryRun) {
                // Delete from Google Drive if applicable
                if ($backup->google_drive_file_id) {
                    try {
                        $driveService->delete($backup->google_drive_file_id);
                    } catch (\Exception $e) {
                        Log::warning('[BackupClean] Failed to delete from Google Drive', [
                            'file' => $backup->google_drive_file_id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Delete local file if it exists
                if ($backup->path && file_exists($backup->path)) {
                    unlink($backup->path);
                }

                $freedBytes += $backup->size;
                $backup->delete();
                $deletedCount++;
            }
        }

        if ($dryRun) {
            $this->warn("  Would delete {$allToDelete->count()} backups");
        } else {
            $freedMB = round($freedBytes / 1024 / 1024, 2);
            $this->info("✅ Cleaned up {$deletedCount} backups, freed {$freedMB} MB");

            Log::info('[BackupClean] Cleanup completed', [
                'deleted' => $deletedCount,
                'freed_bytes' => $freedBytes,
            ]);
        }

        return self::SUCCESS;
    }
}
