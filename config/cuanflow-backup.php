<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Frequency
    |--------------------------------------------------------------------------
    |
    | How often should automated backups run?
    | Supported: "hourly", "daily", "weekly"
    |
    */
    'frequency' => env('BACKUP_FREQUENCY', 'daily'),

    /*
    |--------------------------------------------------------------------------
    | Backup Time (for daily/weekly)
    |--------------------------------------------------------------------------
    |
    | At what time (24h format) should scheduled backups run?
    |
    */
    'scheduled_time' => '02:00',

    /*
    |--------------------------------------------------------------------------
    | Backup Timezone
    |--------------------------------------------------------------------------
    */
    'timezone' => 'Asia/Jakarta',

    /*
    |--------------------------------------------------------------------------
    | Paths to Backup
    |--------------------------------------------------------------------------
    |
    | List of directories to include in file backups.
    |
    */
    'paths' => [
        storage_path('app/public'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paths to Exclude
    |--------------------------------------------------------------------------
    */
    'exclude_paths' => [
        storage_path('app/backup-temp'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Temporary Directory
    |--------------------------------------------------------------------------
    |
    | Where temporary backup files are stored before upload.
    |
    */
    'temp_path' => storage_path('app/backup-temp'),

    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    |
    | Enable AES-256-CBC encryption for backup files before upload.
    | The encryption key should be set in your .env file.
    |
    */
    'encryption' => [
        'enabled' => true,
        'key' => env('BACKUP_ENCRYPTION_KEY'),
        'cipher' => 'aes-256-cbc',
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Drive Configuration
    |--------------------------------------------------------------------------
    */
    'google_drive' => [
        'enabled' => true,
        'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
        'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID'),
    ],
    /*
    |--------------------------------------------------------------------------
    | Retention Policy
    |--------------------------------------------------------------------------
    |
    | How many backups to keep. Old backups beyond these limits
    | will be automatically deleted by the backup:clean command.
    |
    */
    'retention' => [
        'daily_backups' => 7,    // Keep last 7 daily backups
        'weekly_backups' => 4,   // Keep last 4 weekly backups (oldest daily after 7 days)
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    |
    | Email address to receive backup notifications.
    | Set to null to disable email notifications (will still log).
    |
    */
    'notify_email' => env('BACKUP_NOTIFY_EMAIL'),

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Which database connection to back up.
    | Uses single transaction for InnoDB to avoid locking.
    |
    */
    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
        'use_single_transaction' => true,
    ],

];
