<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use Throwable;

class DatabaseBackupWeeklyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-database-weekly
                            {--connection= : The specific database connection to backup}
                            {--no-encrypt : Skip AES-256-GCM encryption}
                            {--key= : Custom 256-bit encryption key (base64 or raw)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a secure local MySQL database backup with GZIP compression, AES-256-GCM encryption, and retention management';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $backupService): int
    {
        $this->info("============================================================");
        $this->info(" Shaltout Gym System - Automated Local Database Backup Engine");
        $this->info("============================================================");

        $connection = $this->option('connection');
        $encrypt = !$this->option('no-encrypt');
        $customKey = $this->option('key');

        $options = [
            'encrypt' => $encrypt,
        ];

        if ($connection) {
            $options['connection'] = $connection;
        }

        if ($customKey) {
            $options['encryption_key'] = $customKey;
        }

        $this->comment("Starting database backup process...");

        try {
            $result = $backupService->createBackup($options);

            $this->newLine();
            $this->info("✔ Backup completed successfully!");
            $this->newLine();

            $this->table(
                ['Property', 'Value'],
                [
                    ['Database', $result['database']],
                    ['Connection', $result['connection']],
                    ['Driver', $result['driver']],
                    ['Backup Filename', $result['filename']],
                    ['Backup Size', $result['size_formatted']],
                    ['Execution Duration', $result['duration_seconds'] . ' seconds'],
                    ['Encrypted', $result['encrypted'] ? 'Yes (AES-256-GCM)' : 'No (GZIP only)'],
                    ['Tables Processed', $result['tables_count']],
                    ['Total Rows Dumped', $result['rows_count']],
                    ['Pruned Old Backups', $result['pruned_count']],
                    ['Storage Location', $result['storage_path']],
                ]
            );

            $this->newLine();
            $this->info("Backup stored securely in private local storage.");

            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->newLine();
            $this->error("✖ Database backup failed!");
            $this->line("<fg=red>Error:</> " . $e->getMessage());
            $this->newLine();

            // Note: Never output credentials or sensitive tokens in error messages
            return Command::FAILURE;
        }
    }
}

