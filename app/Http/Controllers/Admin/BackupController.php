<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\RunBackupJob;
use App\Models\BackupLog;
use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin controller for the Backup Manager dashboard.
 *
 * Provides views and actions for monitoring backup history,
 * triggering manual backups, downloading, retrying, and
 * deleting backup records.
 */
class BackupController extends Controller
{
    public function __construct(
        protected GoogleDriveService $driveService,
    ) {}

    /**
     * Display the backup dashboard with stats and history.
     */
    public function index(Request $request)
    {
        // Backup history with pagination
        $backups = BackupLog::orderBy('created_at', 'desc')
            ->paginate(15);

        // Stats
        $totalBackups = BackupLog::count();
        $successfulBackups = BackupLog::where('status', 'completed')->count();
        $failedBackups = BackupLog::where('status', 'failed')->count();
        $totalSize = BackupLog::where('status', 'completed')->sum('size');

        // Latest successful backup
        $latestBackup = BackupLog::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();

        // Google Drive status
        $googleDriveConfigured = $this->driveService->isConfigured();

        return view('admin.backups.index', compact(
            'backups',
            'totalBackups',
            'successfulBackups',
            'failedBackups',
            'totalSize',
            'latestBackup',
            'googleDriveConfigured',
        ));
    }

    /**
     * Dispatch a manual backup job.
     */
    public function run(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'full');

        if (! in_array($type, ['full', 'database', 'files'])) {
            return back()->with('error', 'Tipe backup tidak valid.');
        }

        $userId = auth()->id();

        RunBackupJob::dispatch($type, $userId);

        $typeLabels = [
            'full' => 'Full (Database + Files)',
            'database' => 'Database Only',
            'files' => 'Files Only',
        ];

        return back()->with('success', "Backup {$typeLabels[$type]} telah dijadwalkan dan akan berjalan di background.");
    }

    /**
     * Delete a backup record and its Google Drive file.
     */
    public function destroy(BackupLog $backup): RedirectResponse
    {
        try {
            // Delete from Google Drive if applicable
            if ($backup->google_drive_file_id) {
                $this->driveService->delete($backup->google_drive_file_id);
            }

            // Delete local file if exists
            if ($backup->path && file_exists($backup->path)) {
                unlink($backup->path);
            }

            $filename = $backup->filename;
            $backup->delete();

            Log::info('[BackupController] Backup deleted', ['filename' => $filename]);

            return back()->with('success', "Backup '{$filename}' berhasil dihapus.");

        } catch (\Exception $e) {
            Log::error('[BackupController] Failed to delete backup', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', "Gagal menghapus backup: {$e->getMessage()}");
        }
    }

    /**
     * Download a backup file.
     *
     * For Google Drive backups, streams from Drive.
     * For local backups, streams from disk.
     */
    public function download(BackupLog $backup): StreamedResponse|RedirectResponse
    {
        if ($backup->status !== 'completed') {
            return back()->with('error', 'Tidak dapat mengunduh backup yang gagal.');
        }

        try {
            // Google Drive download
            if ($backup->google_drive_file_id && $this->driveService->isConfigured()) {
                $tempPath = storage_path("app/backup-temp/{$backup->filename}");

                $downloaded = $this->driveService->download($backup->google_drive_file_id, $tempPath);

                if (! $downloaded) {
                    return back()->with('error', 'Gagal mengunduh dari Google Drive.');
                }

                return response()->streamDownload(function () use ($tempPath) {
                    readfile($tempPath);
                    // Clean up temp file after download
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                }, $backup->filename, [
                    'Content-Type' => 'application/octet-stream',
                ]);
            }

            // Local file download
            if ($backup->path && file_exists($backup->path)) {
                return response()->streamDownload(function () use ($backup) {
                    readfile($backup->path);
                }, $backup->filename, [
                    'Content-Type' => 'application/octet-stream',
                ]);
            }

            return back()->with('error', 'File backup tidak ditemukan.');

        } catch (\Exception $e) {
            Log::error('[BackupController] Download failed', [
                'backup_id' => $backup->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', "Gagal mengunduh backup: {$e->getMessage()}");
        }
    }

    /**
     * Retry a failed backup by dispatching a new job with the same type.
     */
    public function retry(BackupLog $backup): RedirectResponse
    {
        if ($backup->status !== 'failed') {
            return back()->with('error', 'Hanya backup yang gagal yang dapat di-retry.');
        }

        // Extract the original type from the backup type field
        $type = str_replace('gdrive_', '', $backup->type);
        if (! in_array($type, ['full', 'database', 'files'])) {
            $type = 'full';
        }

        $userId = auth()->id();

        RunBackupJob::dispatch($type, $userId);

        return back()->with('success', "Retry backup {$type} telah dijadwalkan.");
    }

    /**
     * Format bytes to human readable string.
     */
    public static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
