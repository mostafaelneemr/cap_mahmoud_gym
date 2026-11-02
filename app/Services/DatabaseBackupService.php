<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use PDO;
use RuntimeException;
use Throwable;

class DatabaseBackupService
{
    public const GCM_HEADER = 'SHALTOUT_GCM_V1';
    public const GCM_CIPHER = 'aes-256-gcm';
    public const GCM_IV_LENGTH = 12; // 96 bits recommended for GCM
    public const GCM_TAG_LENGTH = 16; // 128 bits authentication tag

    /**
     * Create a secure local database backup.
     *
     * @param array $options
     * @return array
     * @throws Throwable
     */
    public function createBackup(array $options = []): array
    {
        $startTime = microtime(true);

        $connectionName = $options['connection'] ?? Config::get('database.default', 'mysql');
        $connectionConfig = Config::get("database.connections.{$connectionName}");

        if (!$connectionConfig) {
            throw new InvalidArgumentException("Database connection [{$connectionName}] is not configured.");
        }

        $driver = $connectionConfig['driver'] ?? 'mysql';
        if (!in_array($driver, ['mysql', 'sqlite'], true)) {
            throw new InvalidArgumentException("Database driver [{$driver}] is not supported for backup. Supported: mysql, sqlite.");
        }

        $databaseName = $connectionConfig['database'] ?? 'database';
        if ($driver === 'sqlite') {
            $databaseName = basename($databaseName, '.sqlite');
        }

        $storageDirectory = $options['storage_path'] ?? Config::get('backup.storage_path', storage_path('app/backups/database'));
        $this->ensureSafeStorageDirectory($storageDirectory);

        $encryptionEnabled = $options['encrypt'] ?? Config::get('backup.encryption_enabled', true);
        $encryptionKey = $options['encryption_key'] ?? Config::get('backup.encryption_key', env('BACKUP_ENCRYPTION_KEY'));
        $chunkSize = (int) ($options['chunk_size'] ?? Config::get('backup.chunk_size', 1000));
        $retentionCount = (int) ($options['retention_count'] ?? Config::get('backup.retention_count', 4));

        if ($encryptionEnabled && empty($encryptionKey)) {
            throw new InvalidArgumentException(
                "Backup encryption is enabled, but no BACKUP_ENCRYPTION_KEY was provided. " .
                "Set BACKUP_ENCRYPTION_KEY in .env, pass --key, or use --no-encrypt to disable."
            );
        }

        $normalizedKey = $encryptionEnabled ? $this->normalizeKey((string) $encryptionKey) : null;

        $timestamp = date('Y-m-d_His');
        $uniqueId = bin2hex(random_bytes(6));
        $tempGzFile = $storageDirectory . DIRECTORY_SEPARATOR . ".tmp_{$uniqueId}.sql.gz";
        $tempEncFile = $storageDirectory . DIRECTORY_SEPARATOR . ".tmp_{$uniqueId}.sql.gz.enc";

        Log::info("Starting local database backup", [
            'database' => $databaseName,
            'connection' => $connectionName,
            'driver' => $driver,
            'encryption' => $encryptionEnabled ? 'AES-256-GCM' : 'none',
        ]);

        $tablesCount = 0;
        $rowsCount = 0;

        try {
            // Step 1: Stream SQL Dump directly into GZIP stream (No raw SQL temp file on disk)
            $gzHandle = @gzopen($tempGzFile, 'wb9');
            if ($gzHandle === false) {
                throw new RuntimeException("Failed to open temporary compressed file for writing: [{$tempGzFile}]");
            }

            try {
                $pdo = DB::connection($connectionName)->getPdo();
                $dumpStats = $this->streamDumpToGzip($pdo, $databaseName, $driver, $gzHandle, $chunkSize);
                $tablesCount = $dumpStats['tables_count'];
                $rowsCount = $dumpStats['rows_count'];
            } finally {
                gzclose($gzHandle);
            }

            // Step 2: Verify GZIP integrity before proceeding
            $this->verifyGzipIntegrity($tempGzFile);

            $finalTempFile = $tempGzFile;
            $finalExtension = '.sql.gz';

            // Step 3: Encrypt using AES-256-GCM if enabled
            if ($encryptionEnabled && $normalizedKey !== null) {
                $this->encryptGzipFile($tempGzFile, $tempEncFile, $normalizedKey);

                // Verify encryption container
                $this->verifyEncryptedFile($tempEncFile, $normalizedKey);

                // Safely delete unencrypted compressed temp file
                if (file_exists($tempGzFile)) {
                    @unlink($tempGzFile);
                }

                $finalTempFile = $tempEncFile;
                $finalExtension = '.sql.gz.enc';
            }

            // Step 4: Atomic rename to final non-temporary filename
            $finalFilename = "backup-{$databaseName}-{$timestamp}{$finalExtension}";
            $finalPath = $storageDirectory . DIRECTORY_SEPARATOR . $finalFilename;

            if (file_exists($finalPath)) {
                throw new RuntimeException("Backup file already exists: [{$finalPath}]. Operation aborted to prevent overwrite.");
            }

            if (!@rename($finalTempFile, $finalPath)) {
                throw new RuntimeException("Failed to finalize backup file: could not rename [{$finalTempFile}] to [{$finalPath}].");
            }

            $sizeBytes = (int) filesize($finalPath);
            $durationSeconds = round(microtime(true) - $startTime, 2);

            // Step 5: Apply retention policy (only after new backup is successfully created and verified)
            $prunedCount = $this->pruneOldBackups($storageDirectory, $retentionCount);

            Log::info("Local database backup completed successfully", [
                'filename' => $finalFilename,
                'size' => $this->formatBytes($sizeBytes),
                'duration_seconds' => $durationSeconds,
                'tables' => $tablesCount,
                'rows' => $rowsCount,
                'pruned_backups' => $prunedCount,
            ]);

            return [
                'success' => true,
                'filename' => $finalFilename,
                'file_path' => $finalPath,
                'storage_path' => $storageDirectory,
                'size_bytes' => $sizeBytes,
                'size_formatted' => $this->formatBytes($sizeBytes),
                'duration_seconds' => $durationSeconds,
                'database' => $databaseName,
                'connection' => $connectionName,
                'driver' => $driver,
                'encrypted' => $encryptionEnabled,
                'tables_count' => $tablesCount,
                'rows_count' => $rowsCount,
                'pruned_count' => $prunedCount,
            ];

        } catch (Throwable $e) {
            // Clean up any remaining temporary files on failure
            if (file_exists($tempGzFile)) {
                @unlink($tempGzFile);
            }
            if (file_exists($tempEncFile)) {
                @unlink($tempEncFile);
            }

            Log::error("Local database backup failed", [
                'database' => $databaseName,
                'connection' => $connectionName,
                'error' => $e->getMessage(),
                'duration_seconds' => round(microtime(true) - $startTime, 2),
            ]);

            throw $e;
        }
    }

    /**
     * Stream SQL dump into an active GZIP handle.
     */
    protected function streamDumpToGzip(PDO $pdo, string $databaseName, string $driver, $gzHandle, int $chunkSize): array
    {
        $tablesCount = 0;
        $rowsCount = 0;

        // Header SQL block
        $header = "-- ============================================================\n"
            . "-- Shaltout Gym Management System - Automated Database Backup\n"
            . "-- Generated: " . date('Y-m-d H:i:s T') . "\n"
            . "-- Driver: {$driver}\n"
            . "-- Database: {$databaseName}\n"
            . "-- ============================================================\n\n";

        if ($driver === 'mysql') {
            $header .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n"
                . "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n"
                . "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n"
                . "/*!50503 SET NAMES utf8mb4 */;\n"
                . "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;\n"
                . "/*!40103 SET TIME_ZONE='+00:00' */;\n"
                . "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n"
                . "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n"
                . "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n"
                . "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n";
        } elseif ($driver === 'sqlite') {
            $header .= "PRAGMA foreign_keys = OFF;\n\n";
        }

        gzwrite($gzHandle, $header);

        if ($driver === 'mysql') {
            // Consistency snapshot transaction for InnoDB
            try {
                $pdo->exec("START TRANSACTION /*!40108 WITH CONSISTENT SNAPSHOT */");
            } catch (Throwable $t) {
                // Ignore if not supported on specific engine
            }

            // Fetch Base Tables
            $tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            $tables = $tablesStmt ? $tablesStmt->fetchAll(PDO::FETCH_NUM) : [];

            foreach ($tables as $tableRow) {
                $tableName = $tableRow[0];
                $tablesCount++;

                // Table structure
                $tableHeader = "--\n-- Table structure for table `{$tableName}`\n--\n\n"
                    . "DROP TABLE IF EXISTS `{$tableName}`;\n"
                    . "/*!40101 SET @saved_cs_client     = @@character_set_client */;\n"
                    . "/*!50503 SET character_set_client = utf8mb4 */;\n";
                gzwrite($gzHandle, $tableHeader);

                $createStmt = $pdo->query("SHOW CREATE TABLE `{$tableName}`");
                if (!$createStmt) {
                    throw new RuntimeException("Failed to read schema definition for table `{$tableName}`.");
                }
                $createRow = $createStmt->fetch(PDO::FETCH_ASSOC);
                $createSql = $createRow['Create Table'] ?? '';
                gzwrite($gzHandle, $createSql . ";\n" . "/*!40101 SET character_set_client = @saved_cs_client */;\n\n");

                // Table Data streaming
                $dataHeader = "--\n-- Dumping data for table `{$tableName}`\n--\n\n"
                    . "/*!40000 ALTER TABLE `{$tableName}` DISABLE KEYS */;\n";
                gzwrite($gzHandle, $dataHeader);

                $selectStmt = $pdo->query("SELECT * FROM `{$tableName}`");
                if ($selectStmt) {
                    $buffer = [];
                    $batchSize = 0;

                    while ($row = $selectStmt->fetch(PDO::FETCH_ASSOC)) {
                        $rowsCount++;
                        $values = [];
                        foreach ($row as $val) {
                            if ($val === null) {
                                $values[] = 'NULL';
                            } elseif (is_int($val) || is_float($val)) {
                                $values[] = (string) $val;
                            } else {
                                $values[] = $pdo->quote((string) $val);
                            }
                        }
                        $buffer[] = '(' . implode(',', $values) . ')';
                        $batchSize++;

                        if ($batchSize >= 100) {
                            $insertSql = "INSERT INTO `{$tableName}` VALUES " . implode(",\n", $buffer) . ";\n";
                            gzwrite($gzHandle, $insertSql);
                            $buffer = [];
                            $batchSize = 0;
                        }
                    }

                    if (!empty($buffer)) {
                        $insertSql = "INSERT INTO `{$tableName}` VALUES " . implode(",\n", $buffer) . ";\n";
                        gzwrite($gzHandle, $insertSql);
                    }
                }

                gzwrite($gzHandle, "/*!40000 ALTER TABLE `{$tableName}` ENABLE KEYS */;\n\n");
            }

            // Fetch Views
            $viewsStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
            $views = $viewsStmt ? $viewsStmt->fetchAll(PDO::FETCH_NUM) : [];

            foreach ($views as $viewRow) {
                $viewName = $viewRow[0];
                $viewStmt = $pdo->query("SHOW CREATE VIEW `{$viewName}`");
                if ($viewStmt) {
                    $viewRowData = $viewStmt->fetch(PDO::FETCH_ASSOC);
                    $createViewSql = $viewRowData['Create View'] ?? '';
                    gzwrite($gzHandle, "--\n-- View `{$viewName}`\n--\n\n" . $createViewSql . ";\n\n");
                }
            }

            // Commit snapshot transaction
            try {
                $pdo->exec("COMMIT");
            } catch (Throwable $t) {
                // Ignore
            }

            // MySQL Footer block
            $footer = "/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;\n"
                . "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n"
                . "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n"
                . "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n"
                . "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n"
                . "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n"
                . "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n"
                . "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n"
                . "-- Backup completed successfully.\n";
            gzwrite($gzHandle, $footer);

        } elseif ($driver === 'sqlite') {
            // SQLite support for isolated feature tests
            $tablesStmt = $pdo->query("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $tables = $tablesStmt ? $tablesStmt->fetchAll(PDO::FETCH_ASSOC) : [];

            foreach ($tables as $table) {
                $tableName = $table['name'];
                $tableSql = $table['sql'];
                $tablesCount++;

                gzwrite($gzHandle, "DROP TABLE IF EXISTS \"{$tableName}\";\n");
                gzwrite($gzHandle, $tableSql . ";\n\n");

                $selectStmt = $pdo->query("SELECT * FROM \"{$tableName}\"");
                if ($selectStmt) {
                    $buffer = [];
                    $batchSize = 0;

                    while ($row = $selectStmt->fetch(PDO::FETCH_ASSOC)) {
                        $rowsCount++;
                        $values = [];
                        foreach ($row as $val) {
                            if ($val === null) {
                                $values[] = 'NULL';
                            } elseif (is_int($val) || is_float($val)) {
                                $values[] = (string) $val;
                            } else {
                                $values[] = $pdo->quote((string) $val);
                            }
                        }
                        $buffer[] = '(' . implode(',', $values) . ')';
                        $batchSize++;

                        if ($batchSize >= 100) {
                            $insertSql = "INSERT INTO \"{$tableName}\" VALUES " . implode(",\n", $buffer) . ";\n";
                            gzwrite($gzHandle, $insertSql);
                            $buffer = [];
                            $batchSize = 0;
                        }
                    }

                    if (!empty($buffer)) {
                        $insertSql = "INSERT INTO \"{$tableName}\" VALUES " . implode(",\n", $buffer) . ";\n";
                        gzwrite($gzHandle, $insertSql);
                    }
                }
                gzwrite($gzHandle, "\n");
            }

            gzwrite($gzHandle, "PRAGMA foreign_keys = ON;\n-- SQLite Backup completed.\n");
        }

        return [
            'tables_count' => $tablesCount,
            'rows_count' => $rowsCount,
        ];
    }

    /**
     * Verify GZIP file integrity.
     */
    public function verifyGzipIntegrity(string $filePath): bool
    {
        if (!file_exists($filePath) || filesize($filePath) === 0) {
            throw new RuntimeException("GZIP integrity check failed: file does not exist or is empty [{$filePath}].");
        }

        $gz = @gzopen($filePath, 'rb');
        if ($gz === false) {
            throw new RuntimeException("GZIP integrity check failed: cannot open file [{$filePath}].");
        }

        try {
            while (!gzeof($gz)) {
                $chunk = gzread($gz, 65536);
                if ($chunk === false) {
                    throw new RuntimeException("GZIP integrity check failed: error reading compressed stream [{$filePath}].");
                }
            }
        } finally {
            gzclose($gz);
        }

        return true;
    }

    /**
     * Encrypt a GZIP file using AES-256-GCM authenticated encryption.
     */
    public function encryptGzipFile(string $sourceGzPath, string $destEncPath, string $binaryKey): void
    {
        if (strlen($binaryKey) !== 32) {
            throw new InvalidArgumentException("AES-256-GCM requires a 32-byte key (256 bits).");
        }

        $sourceHandle = @fopen($sourceGzPath, 'rb');
        if ($sourceHandle === false) {
            throw new RuntimeException("Cannot open unencrypted file for reading: [{$sourceGzPath}]");
        }

        $plainData = stream_get_contents($sourceHandle);
        fclose($sourceHandle);

        if ($plainData === false) {
            throw new RuntimeException("Failed to read unencrypted compressed data from [{$sourceGzPath}].");
        }

        $iv = random_bytes(self::GCM_IV_LENGTH);
        $tag = '';

        $cipherText = openssl_encrypt(
            $plainData,
            self::GCM_CIPHER,
            $binaryKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            self::GCM_HEADER, // Additional Authenticated Data (AAD)
            self::GCM_TAG_LENGTH
        );

        if ($cipherText === false) {
            throw new RuntimeException("Encryption failed: " . openssl_error_string());
        }

        $destHandle = @fopen($destEncPath, 'wb');
        if ($destHandle === false) {
            throw new RuntimeException("Cannot open destination encrypted file for writing: [{$destEncPath}]");
        }

        try {
            // Container layout:
            // 1. Header (15 bytes)
            // 2. IV length (1 byte) + IV (12 bytes)
            // 3. Tag length (1 byte) + Tag (16 bytes)
            // 4. Ciphertext (variable length)
            fwrite($destHandle, self::GCM_HEADER);
            fwrite($destHandle, chr(self::GCM_IV_LENGTH));
            fwrite($destHandle, $iv);
            fwrite($destHandle, chr(self::GCM_TAG_LENGTH));
            fwrite($destHandle, $tag);
            fwrite($destHandle, $cipherText);
        } finally {
            fclose($destHandle);
        }
    }

    /**
     * Decrypt an encrypted backup file to a destination GZIP file.
     */
    public function decryptBackupFile(string $sourceEncPath, string $destGzPath, string $binaryKey): void
    {
        if (strlen($binaryKey) !== 32) {
            throw new InvalidArgumentException("AES-256-GCM requires a 32-byte key (256 bits).");
        }

        if (!file_exists($sourceEncPath)) {
            throw new RuntimeException("Encrypted backup file does not exist: [{$sourceEncPath}]");
        }

        $handle = @fopen($sourceEncPath, 'rb');
        if ($handle === false) {
            throw new RuntimeException("Cannot open encrypted backup file for reading: [{$sourceEncPath}]");
        }

        try {
            $header = fread($handle, strlen(self::GCM_HEADER));
            if ($header !== self::GCM_HEADER) {
                throw new RuntimeException("Invalid backup format or corrupted header in [{$sourceEncPath}].");
            }

            $ivLen = ord((string) fread($handle, 1));
            if ($ivLen !== self::GCM_IV_LENGTH) {
                throw new RuntimeException("Invalid IV length in encrypted container [{$sourceEncPath}].");
            }
            $iv = fread($handle, $ivLen);

            $tagLen = ord((string) fread($handle, 1));
            if ($tagLen !== self::GCM_TAG_LENGTH) {
                throw new RuntimeException("Invalid authentication tag length in encrypted container [{$sourceEncPath}].");
            }
            $tag = fread($handle, $tagLen);

            $cipherText = stream_get_contents($handle);
            if ($cipherText === false) {
                throw new RuntimeException("Failed to read ciphertext from encrypted container.");
            }
        } finally {
            fclose($handle);
        }

        $plainData = openssl_decrypt(
            $cipherText,
            self::GCM_CIPHER,
            $binaryKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            self::GCM_HEADER
        );

        if ($plainData === false) {
            throw new RuntimeException("Decryption failed. Invalid encryption key or corrupted/tampered backup file.");
        }

        $destHandle = @fopen($destGzPath, 'wb');
        if ($destHandle === false) {
            throw new RuntimeException("Cannot open decrypted output file for writing: [{$destGzPath}]");
        }

        try {
            fwrite($destHandle, $plainData);
        } finally {
            fclose($destHandle);
        }

        // Verify decrypted stream integrity
        $this->verifyGzipIntegrity($destGzPath);
    }

    /**
     * Verify an encrypted file container.
     */
    public function verifyEncryptedFile(string $filePath, string $binaryKey): bool
    {
        $tempTestGz = dirname($filePath) . DIRECTORY_SEPARATOR . '.test_' . bin2hex(random_bytes(6)) . '.tmp';
        try {
            $this->decryptBackupFile($filePath, $tempTestGz, $binaryKey);
            return true;
        } finally {
            if (file_exists($tempTestGz)) {
                @unlink($tempTestGz);
            }
        }
    }

    /**
     * Restore a backup file into a database connection (for testing & safe local recovery).
     */
    public function restoreBackup(string $backupFilePath, ?string $connection = null, ?string $key = null): array
    {
        $connectionName = $connection ?? Config::get('database.default', 'mysql');
        $pdo = DB::connection($connectionName)->getPdo();

        $tempDecryptedGz = null;
        $fileToRead = $backupFilePath;

        try {
            // Check if file is encrypted
            $isEncrypted = false;
            $handle = @fopen($backupFilePath, 'rb');
            if ($handle) {
                $head = fread($handle, strlen(self::GCM_HEADER));
                fclose($handle);
                if ($head === self::GCM_HEADER) {
                    $isEncrypted = true;
                }
            }

            if ($isEncrypted) {
                if (empty($key)) {
                    $key = Config::get('backup.encryption_key', env('BACKUP_ENCRYPTION_KEY'));
                }
                if (empty($key)) {
                    throw new InvalidArgumentException("Cannot restore encrypted backup without an encryption key.");
                }
                $binaryKey = $this->normalizeKey($key);
                $tempDecryptedGz = dirname($backupFilePath) . DIRECTORY_SEPARATOR . '.restore_' . bin2hex(random_bytes(6)) . '.tmp.gz';
                $this->decryptBackupFile($backupFilePath, $tempDecryptedGz, $binaryKey);
                $fileToRead = $tempDecryptedGz;
            }

            $gz = @gzopen($fileToRead, 'rb');
            if ($gz === false) {
                throw new RuntimeException("Cannot open backup file for restoration: [{$fileToRead}]");
            }

            $currentQuery = '';
            $executedStatements = 0;

            try {
                while (!gzeof($gz)) {
                    $line = gzgets($gz, 1048576);
                    if ($line === false) {
                        break;
                    }

                    $trimmed = trim($line);
                    if ($trimmed === '' || str_starts_with($trimmed, '--') || (str_starts_with($trimmed, '/*') && !str_starts_with($trimmed, '/*!'))) {
                        continue;
                    }

                    $currentQuery .= $line;
                    if (str_ends_with($trimmed, ';')) {
                        $pdo->exec($currentQuery);
                        $executedStatements++;
                        $currentQuery = '';
                    }
                }
            } finally {
                gzclose($gz);
            }

            return [
                'success' => true,
                'connection' => $connectionName,
                'executed_statements' => $executedStatements,
            ];

        } finally {
            if ($tempDecryptedGz !== null && file_exists($tempDecryptedGz)) {
                @unlink($tempDecryptedGz);
            }
        }
    }

    /**
     * Apply retention policy: retain the latest N valid backups, prune older ones.
     */
    public function pruneOldBackups(string $storageDirectory, int $retentionCount = 4): int
    {
        if (!is_dir($storageDirectory) || $retentionCount <= 0) {
            return 0;
        }

        $files = scandir($storageDirectory);
        if ($files === false) {
            return 0;
        }

        $validBackups = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || str_starts_with($file, '.')) {
                continue;
            }

            // Strictly match backup naming pattern
            if (preg_match('/^backup-.+\.(sql\.gz|sql\.gz\.enc)$/', $file)) {
                $fullPath = $storageDirectory . DIRECTORY_SEPARATOR . $file;
                if (is_file($fullPath) && filesize($fullPath) > 0) {
                    $validBackups[] = [
                        'filename' => $file,
                        'path' => $fullPath,
                        'mtime' => filemtime($fullPath),
                    ];
                }
            }
        }

        // Sort descending by modification time (newest first)
        usort($validBackups, function ($a, $b) {
            return $b['mtime'] <=> $a['mtime'];
        });

        // Never prune if valid backups are less than or equal to retention count
        // Never delete the only valid backup
        if (count($validBackups) <= $retentionCount) {
            return 0;
        }

        $backupsToPrune = array_slice($validBackups, $retentionCount);
        $prunedCount = 0;

        foreach ($backupsToPrune as $backup) {
            try {
                if (@unlink($backup['path'])) {
                    $prunedCount++;
                    Log::info("Pruned old database backup according to retention policy", [
                        'file' => $backup['filename'],
                    ]);
                } else {
                    Log::warning("Failed to delete expired backup file", [
                        'file' => $backup['filename'],
                    ]);
                }
            } catch (Throwable $t) {
                Log::warning("Exception while pruning backup file: " . $t->getMessage(), [
                    'file' => $backup['filename'],
                ]);
            }
        }

        return $prunedCount;
    }

    /**
     * Ensure the storage directory exists and is safe (outside public/).
     */
    public function ensureSafeStorageDirectory(string $path): void
    {
        $realPublic = realpath(public_path());
        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        // Security check: Never store inside public web root
        if ($realPublic && str_starts_with(realpath($path) ?: $normalizedPath, $realPublic)) {
            throw new RuntimeException("Security violation: Backup storage path [{$path}] must NOT reside inside the public web root.");
        }

        if (!is_dir($path)) {
            if (!@mkdir($path, 0750, true) && !is_dir($path)) {
                throw new RuntimeException("Failed to create backup storage directory: [{$path}]. Check directory permissions.");
            }
        }

        // Write safety barriers (.gitignore & index.html)
        $gitignorePath = $path . DIRECTORY_SEPARATOR . '.gitignore';
        if (!file_exists($gitignorePath)) {
            @file_put_contents($gitignorePath, "*\n!.gitignore\n!index.html\n");
        }

        $indexPath = $path . DIRECTORY_SEPARATOR . 'index.html';
        if (!file_exists($indexPath)) {
            @file_put_contents($indexPath, "<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Directory access is forbidden.</h1></body></html>");
        }
    }

    /**
     * Normalize encryption key (supports base64:, hex, or raw 32-byte string).
     */
    public function normalizeKey(string $key): string
    {
        $key = trim($key);

        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded === false) {
                throw new InvalidArgumentException("Invalid base64 encoding for backup encryption key.");
            }
            $key = $decoded;
        } elseif (strlen($key) === 64 && ctype_xdigit($key)) {
            $key = hex2bin($key);
        }

        if (strlen($key) !== 32) {
            throw new InvalidArgumentException("Backup encryption key must resolve to exactly 32 bytes (256 bits). Given: " . strlen($key) . " bytes.");
        }

        return $key;
    }

    /**
     * Generate a secure base64-encoded 256-bit key.
     */
    public static function generateEncryptionKey(): string
    {
        return 'base64:' . base64_encode(random_bytes(32));
    }

    /**
     * Format bytes into a human-readable string.
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

