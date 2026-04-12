<?php

namespace App\Services;

use App\Models\BackupLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Core backup service responsible for database dumps,
 * file archiving, encryption, and integrity verification.
 */
class BackupService
{
    /**
     * Temporary directory for backup operations.
     */
    protected string $tempPath;

    public function __construct()
    {
        $this->tempPath = config('cuanflow-backup.temp_path', storage_path('app/backup-temp'));

        if (!is_dir($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
    }

    /**
     * Generate a standardized backup filename.
     *
     * Format: backup_{APP_NAME}_{YYYY-MM-DD_HH-mm-ss}.zip
     */
    public function generateBackupFilename(string $type = 'full'): string
    {
        $appName = Str::slug(config('app.name', 'cuanflow'), '_');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $suffix = $type !== 'full' ? "_{$type}" : '';

        return "backup_{$appName}{$suffix}_{$timestamp}.zip";
    }

    /**
     * Create a MySQL database dump file.
     *
     * Uses mysqldump with single-transaction for InnoDB
     * to avoid table locking during backup.
     *
     * @throws \RuntimeException If database dump fails
     */
    public function createDatabaseDump(): string
    {
        $dbConfig = config('cuanflow-backup.database');
        $connection = config("database.connections.{$dbConfig['connection']}");

        $dumpFile = $this->tempPath . '/db_dump_' . now()->format('Y-m-d_H-i-s') . '.sql';

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s %s %s > %s 2>&1',
            escapeshellarg($connection['host'] ?? '127.0.0.1'),
            escapeshellarg($connection['port'] ?? '3306'),
            escapeshellarg($connection['username'] ?? 'root'),
            !empty($connection['password']) ? '--password=' . escapeshellarg($connection['password']) : '',
            $dbConfig['use_single_transaction'] ? '--single-transaction' : '',
            escapeshellarg($connection['database']),
            escapeshellarg($dumpFile)
        );

        $result = Process::timeout(300)->run($command);

        if (!file_exists($dumpFile) || filesize($dumpFile) === 0) {
            $errorOutput = $result->errorOutput() ?: $result->output();
            throw new \RuntimeException("Database dump failed: {$errorOutput}");
        }

        Log::info('[BackupService] Database dump created', [
            'file' => $dumpFile,
            'size' => filesize($dumpFile),
        ]);

        return $dumpFile;
    }

    /**
     * Create a ZIP archive of configured backup paths.
     *
     * @throws \RuntimeException If zip creation fails
     */
    public function createFilesArchive(): string
    {
        $paths = config('cuanflow-backup.paths', []);
        $excludePaths = config('cuanflow-backup.exclude_paths', []);
        $zipFile = $this->tempPath . '/files_' . now()->format('Y-m-d_H-i-s') . '.zip';

        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip file: {$zipFile}");
        }

        foreach ($paths as $path) {
            if (!is_dir($path) && !is_file($path)) {
                Log::warning("[BackupService] Backup path does not exist: {$path}");
                continue;
            }

            $this->addPathToZip($zip, $path, $excludePaths);
        }

        $zip->close();

        if (!file_exists($zipFile) || filesize($zipFile) === 0) {
            throw new \RuntimeException("Files archive is empty or was not created");
        }

        Log::info('[BackupService] Files archive created', [
            'file' => $zipFile,
            'size' => filesize($zipFile),
        ]);

        return $zipFile;
    }

    /**
     * Create a full backup (database + files) as a single ZIP archive.
     *
     * @throws \RuntimeException If backup creation fails
     */
    public function createFullBackup(): string
    {
        $filename = $this->generateBackupFilename('full');
        $finalZip = $this->tempPath . '/' . $filename;

        $zip = new ZipArchive();
        if ($zip->open($finalZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create final zip: {$finalZip}");
        }

        // Add database dump
        try {
            $dbDump = $this->createDatabaseDump();
            $zip->addFile($dbDump, 'database/' . basename($dbDump));
        } catch (\Exception $e) {
            Log::error('[BackupService] Database dump failed during full backup', ['error' => $e->getMessage()]);
            throw $e;
        }

        // Add storage files
        $paths = config('cuanflow-backup.paths', []);
        $excludePaths = config('cuanflow-backup.exclude_paths', []);

        foreach ($paths as $path) {
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            $this->addPathToZip($zip, $path, $excludePaths, 'files/');
        }

        $zip->close();

        // Clean up temp DB dump
        if (isset($dbDump) && file_exists($dbDump)) {
            unlink($dbDump);
        }

        Log::info('[BackupService] Full backup created', [
            'file' => $finalZip,
            'size' => filesize($finalZip),
        ]);

        return $finalZip;
    }

    /**
     * Create a database-only backup.
     */
    public function createDatabaseBackup(): string
    {
        $filename = $this->generateBackupFilename('database');
        $zipFile = $this->tempPath . '/' . $filename;

        $dbDump = $this->createDatabaseDump();

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            if (file_exists($dbDump)) unlink($dbDump);
            throw new \RuntimeException("Cannot create zip: {$zipFile}");
        }

        $zip->addFile($dbDump, basename($dbDump));
        $zip->close();

        if (file_exists($dbDump)) unlink($dbDump);

        return $zipFile;
    }

    /**
     * Create a files-only backup.
     */
    public function createFilesBackup(): string
    {
        $filename = $this->generateBackupFilename('files');
        $zipFile = $this->tempPath . '/' . $filename;

        $paths = config('cuanflow-backup.paths', []);
        $excludePaths = config('cuanflow-backup.exclude_paths', []);

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip: {$zipFile}");
        }

        foreach ($paths as $path) {
            if (!is_dir($path) && !is_file($path)) continue;
            $this->addPathToZip($zip, $path, $excludePaths);
        }

        $zip->close();

        return $zipFile;
    }

    /**
     * Encrypt a file using AES-256-CBC.
     *
     * Generates a random IV, encrypts the file contents,
     * and prepends the IV to the encrypted output.
     *
     * @param string $filePath Path to the file to encrypt
     * @return string Path to the encrypted file (.enc extension)
     * @throws \RuntimeException If encryption fails
     */
    public function encryptFile(string $filePath): string
    {
        $key = config('cuanflow-backup.encryption.key');

        if (empty($key)) {
            throw new \RuntimeException('Backup encryption key is not configured. Set BACKUP_ENCRYPTION_KEY in .env');
        }

        // Decode base64 key or use raw key
        $decodedKey = base64_decode($key, true);
        if ($decodedKey === false || strlen($decodedKey) !== 32) {
            // Use hash of the key to ensure correct length for AES-256
            $decodedKey = hash('sha256', $key, true);
        }

        $cipher = config('cuanflow-backup.encryption.cipher', 'aes-256-cbc');
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encryptedPath = $filePath . '.enc';

        // Read and encrypt in chunks to handle large files
        $inputHandle = fopen($filePath, 'rb');
        $outputHandle = fopen($encryptedPath, 'wb');

        if (!$inputHandle || !$outputHandle) {
            throw new \RuntimeException("Cannot open files for encryption");
        }

        // Write IV at the beginning of the file
        fwrite($outputHandle, $iv);

        // Encrypt the entire file content
        $content = file_get_contents($filePath);
        $encrypted = openssl_encrypt($content, $cipher, $decodedKey, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            fclose($inputHandle);
            fclose($outputHandle);
            throw new \RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        fwrite($outputHandle, $encrypted);

        fclose($inputHandle);
        fclose($outputHandle);

        // Remove original unencrypted file
        unlink($filePath);

        Log::info('[BackupService] File encrypted', [
            'original' => basename($filePath),
            'encrypted' => basename($encryptedPath),
            'size' => filesize($encryptedPath),
        ]);

        return $encryptedPath;
    }

    /**
     * Generate SHA-256 checksum for file integrity verification.
     */
    public function generateChecksum(string $filePath): string
    {
        return hash_file('sha256', $filePath);
    }

    /**
     * Verify file integrity by comparing checksums.
     */
    public function verifyChecksum(string $filePath, string $expectedChecksum): bool
    {
        return hash_file('sha256', $filePath) === $expectedChecksum;
    }

    /**
     * Clean up temporary backup files.
     */
    public function cleanupTempFiles(): void
    {
        $files = glob($this->tempPath . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        Log::info('[BackupService] Temporary files cleaned up');
    }

    /**
     * Recursively add a directory/file to a ZipArchive.
     */
    protected function addPathToZip(ZipArchive $zip, string $path, array $excludePaths = [], string $prefix = ''): void
    {
        // Check exclusions
        foreach ($excludePaths as $exclude) {
            if (str_starts_with(realpath($path) ?: $path, realpath($exclude) ?: $exclude)) {
                return;
            }
        }

        if (is_file($path)) {
            $zip->addFile($path, $prefix . basename($path));
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $realItemPath = $item->getRealPath();

            // Check exclusions for each item
            $excluded = false;
            foreach ($excludePaths as $exclude) {
                $realExclude = realpath($exclude) ?: $exclude;
                if (str_starts_with($realItemPath, $realExclude)) {
                    $excluded = true;
                    break;
                }
            }
            if ($excluded) continue;

            $relativePath = $prefix . substr($realItemPath, strlen(realpath($path)) + 1);

            if ($item->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($realItemPath, $relativePath);
            }
        }
    }
}
