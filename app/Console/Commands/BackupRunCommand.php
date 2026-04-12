<?php

namespace App\Console\Commands;

use App\Jobs\RunBackupJob;
use App\Services\BackupService;
use App\Services\GoogleDriveService;
use Illuminate\Console\Command;

/**
 * Artisan command to trigger a backup.
 *
 * Usage:
 *   php artisan backup:run                 # Full backup via queue
 *   php artisan backup:run --type=database # Database only
 *   php artisan backup:run --type=files    # Files only
 *   php artisan backup:run --sync          # Run synchronously (skip queue)
 */
class BackupRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backup:run
                            {--type=full : Backup type: full, database, or files}
                            {--sync : Run synchronously instead of via queue}';

    /**
     * The console command description.
     */
    protected $description = 'Run a backup (database + files) and upload to Google Drive';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');
        $sync = $this->option('sync');

        if (!in_array($type, ['full', 'database', 'files'])) {
            $this->error("Invalid backup type: {$type}. Use: full, database, or files");
            return self::FAILURE;
        }

        $this->info("🔄 Starting {$type} backup...");

        if ($sync) {
            $this->warn('Running synchronously (blocking). Use queue for production.');

            try {
                $job = new RunBackupJob($type);
                $job->handle(
                    app(BackupService::class),
                    app(GoogleDriveService::class)
                );

                $this->info('✅ Backup completed successfully!');
                return self::SUCCESS;

            } catch (\Exception $e) {
                $this->error("❌ Backup failed: {$e->getMessage()}");
                return self::FAILURE;
            }
        }

        // Dispatch to queue
        RunBackupJob::dispatch($type);
        $this->info("✅ Backup job dispatched to queue. Monitor progress in admin panel.");

        return self::SUCCESS;
    }
}
