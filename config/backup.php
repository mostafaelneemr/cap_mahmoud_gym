<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Storage Path
    |--------------------------------------------------------------------------
    |
    | The absolute directory path where local backups will be stored.
    | Must always reside outside the public/ web root for security.
    |
    */
    'storage_path' => env('BACKUP_STORAGE_PATH', storage_path('app/backups/database')),

    /*
    |--------------------------------------------------------------------------
    | Retention Policy
    |--------------------------------------------------------------------------
    |
    | Number of latest successful weekly backups to retain locally.
    | Older backups exceeding this count will be pruned after a new backup.
    |
    */
    'retention_count' => (int) env('BACKUP_RETENTION_COUNT', 4),

    /*
    |--------------------------------------------------------------------------
    | Backup Encryption
    |--------------------------------------------------------------------------
    |
    | Whether backups should be encrypted using AES-256-GCM.
    | Highly recommended for production environments.
    |
    */
    'encryption_enabled' => (bool) env('BACKUP_ENCRYPTION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Dedicated Backup Encryption Key
    |--------------------------------------------------------------------------
    |
    | 256-bit key for AES-256-GCM encryption.
    | Can be base64-encoded (e.g. 'base64:...') or raw 32 bytes.
    | Must be kept secure and backed up separately.
    |
    */
    'encryption_key' => env('BACKUP_ENCRYPTION_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Query Streaming & Chunk Size
    |--------------------------------------------------------------------------
    |
    | The number of table rows fetched per query batch to avoid memory spikes.
    |
    */
    'chunk_size' => (int) env('BACKUP_CHUNK_SIZE', 1000),

];

