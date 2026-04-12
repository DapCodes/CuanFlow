<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Google Drive service for uploading, downloading,
 * and managing backup files on Google Drive.
 *
 * Uses the Flysystem Google Drive adapter configured
 * as a filesystem disk.
 */
class GoogleDriveService
{
    /**
     * The filesystem disk name for Google Drive.
     */
    protected string $diskName = 'google';

    /**
     * Check if Google Drive is properly configured.
     */
    public function isConfigured(): bool
    {
        $config = config('cuanflow-backup.google_drive');
        $clientId = $config['client_id'] ?? null;
        $clientSecret = $config['client_secret'] ?? null;
        $refreshToken = $config['refresh_token'] ?? null;
        $folderId = $config['folder_id'] ?? null;
        $enabled = $config['enabled'] ?? false;

        Log::info('[GoogleDriveService] Configuration check (OAuth2)', [
            'enabled' => $enabled,
            'client_id_set' => !empty($clientId),
            'refresh_token_set' => !empty($refreshToken),
            'folder_id' => $folderId,
        ]);

        return $enabled
            && !empty($clientId)
            && !empty($clientSecret)
            && !empty($refreshToken)
            && !empty($folderId);
    }

    /**
     * Upload a file to Google Drive.
     *
     * @param string $localPath Absolute path to the local file
     * @param string $remoteName Filename to use on Google Drive
     * @return string|null Google Drive file ID on success, null on failure
     */
    public function upload(string $localPath, string $remoteName): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('[GoogleDriveService] Google Drive is not configured. Skipping upload.');
            return null;
        }

        try {
            $disk = Storage::disk($this->diskName);
            $stream = fopen($localPath, 'rb');

            if (!$stream) {
                throw new \RuntimeException("Cannot open file for upload: {$localPath}");
            }

            $success = $disk->put($remoteName, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }

            if (!$success) {
                // If put returns false, it usually means a Service Account Quota issue
                throw new \RuntimeException("Google Drive upload failed. This is typically due to Service Account storage quota (0MB) or folder permissions. Tip: Use a Shared Drive or ensure the Service Account email has 'Editor' access to the folder.");
            }

            Log::info('[GoogleDriveService] File uploaded to Google Drive', [
                'file' => $remoteName,
                'size' => filesize($localPath),
            ]);

            // Return the filename as the identifier
            return $remoteName;

        } catch (\Exception $e) {
            Log::error('[GoogleDriveService] Upload failed', [
                'file' => $remoteName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a file from Google Drive.
     *
     * @param string $remoteName Remote filename or file ID
     * @return bool True if deleted successfully
     */
    public function delete(string $remoteName): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('[GoogleDriveService] Google Drive not configured. Skipping delete.');
            return false;
        }

        try {
            $disk = Storage::disk($this->diskName);

            if ($disk->exists($remoteName)) {
                $disk->delete($remoteName);

                Log::info('[GoogleDriveService] File deleted from Google Drive', [
                    'file' => $remoteName,
                ]);

                return true;
            }

            Log::warning('[GoogleDriveService] File not found on Google Drive', [
                'file' => $remoteName,
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('[GoogleDriveService] Delete failed', [
                'file' => $remoteName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Download a file from Google Drive to a local path.
     *
     * @param string $remoteName Remote filename
     * @param string $localPath Local path to save the file
     * @return bool True if downloaded successfully
     */
    public function download(string $remoteName, string $localPath): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $disk = Storage::disk($this->diskName);

            if (!$disk->exists($remoteName)) {
                Log::warning('[GoogleDriveService] File not found for download', [
                    'file' => $remoteName,
                ]);
                return false;
            }

            $stream = $disk->readStream($remoteName);

            if (!$stream) {
                throw new \RuntimeException("Cannot read stream from Google Drive: {$remoteName}");
            }

            $localHandle = fopen($localPath, 'wb');
            stream_copy_to_stream($stream, $localHandle);
            fclose($localHandle);

            if (is_resource($stream)) {
                fclose($stream);
            }

            Log::info('[GoogleDriveService] File downloaded from Google Drive', [
                'file' => $remoteName,
                'local_path' => $localPath,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('[GoogleDriveService] Download failed', [
                'file' => $remoteName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * List all files in the backup folder on Google Drive.
     *
     * @return array List of file names
     */
    public function listFiles(): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            $disk = Storage::disk($this->diskName);
            return $disk->files();
        } catch (\Exception $e) {
            Log::error('[GoogleDriveService] Failed to list files', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Check if a file exists on Google Drive.
     */
    public function exists(string $remoteName): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            return Storage::disk($this->diskName)->exists($remoteName);
        } catch (\Exception $e) {
            return false;
        }
    }
}
