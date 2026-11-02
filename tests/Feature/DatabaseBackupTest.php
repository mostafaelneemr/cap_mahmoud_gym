<?php

namespace Tests\Feature;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    protected string $testStorageDir;
    protected string $sqliteDbFile;
    protected string $restoreDbFile;
    protected DatabaseBackupService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DatabaseBackupService();
        $this->testStorageDir = storage_path('framework/testing/backups_' . bin2hex(random_bytes(4)));
        $this->sqliteDbFile = storage_path('framework/testing/test_source_' . bin2hex(random_bytes(4)) . '.sqlite');
        $this->restoreDbFile = storage_path('framework/testing/test_restore_' . bin2hex(random_bytes(4)) . '.sqlite');

        if (!is_dir(dirname($this->sqliteDbFile))) {
            @mkdir(dirname($this->sqliteDbFile), 0777, true);
        }

        // Initialize SQLite files
        touch($this->sqliteDbFile);
        touch($this->restoreDbFile);

        // Setup test connections
        Config::set('database.connections.test_source', [
            'driver' => 'sqlite',
            'database' => $this->sqliteDbFile,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        Config::set('database.connections.test_restore', [
            'driver' => 'sqlite',
            'database' => $this->restoreDbFile,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        // Seed test tables and records in test_source
        $pdo = DB::connection('test_source')->getPdo();
        $pdo->exec("CREATE TABLE trainees (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            notes TEXT,
            weight REAL,
            is_active INTEGER DEFAULT 1,
            avatar BLOB,
            created_at DATETIME
        )");

        $pdo->exec("CREATE TABLE workout_plans (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trainee_id INTEGER,
            title VARCHAR(255),
            FOREIGN KEY (trainee_id) REFERENCES trainees(id)
        )");

        // Insert rows with various datatypes, special characters, and Arabic text
        $stmt = $pdo->prepare("INSERT INTO trainees (name, email, notes, weight, is_active, avatar, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'كابتن محمود شلتوت',
            'coach@shaltout.com',
            "Special notes: Line 1\nLine 2 with \"quotes\" & 'apostrophe' and \\backslash.",
            85.5,
            1,
            bin2hex(random_bytes(16)),
            '2026-11-02 12:00:00',
        ]);

        $stmt->execute([
            'متدرب تجريبي #2',
            'trainee2@example.com',
            null,
            72.0,
            0,
            null,
            '2026-11-02 13:00:00',
        ]);

        $pdo->exec("INSERT INTO workout_plans (trainee_id, title) VALUES (1, 'خطة تضخيم العضلات - أسبوع 1')");
    }

    protected function tearDown(): void
    {
        // Cleanup test files
        if (is_dir($this->testStorageDir)) {
            $files = glob($this->testStorageDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            @rmdir($this->testStorageDir);
        }

        if (file_exists($this->sqliteDbFile)) {
            @unlink($this->sqliteDbFile);
        }
        if (file_exists($this->restoreDbFile)) {
            @unlink($this->restoreDbFile);
        }

        parent::tearDown();
    }

    public function test_can_create_unencrypted_backup_and_preserves_structure_and_data(): void
    {
        $result = $this->service->createBackup([
            'connection' => 'test_source',
            'encrypt' => false,
            'storage_path' => $this->testStorageDir,
        ]);

        $this->assertTrue($result['success']);
        $this->assertFileExists($result['file_path']);
        $this->assertStringEndsWith('.sql.gz', $result['filename']);
        $this->assertGreaterThan(0, $result['size_bytes']);
        $this->assertEquals(2, $result['tables_count']);
        $this->assertEquals(3, $result['rows_count']); // 2 trainees + 1 workout_plan

        // Verify GZIP integrity
        $this->assertTrue($this->service->verifyGzipIntegrity($result['file_path']));

        // Restore into isolated restore connection and verify data
        $restoreResult = $this->service->restoreBackup($result['file_path'], 'test_restore');
        $this->assertTrue($restoreResult['success']);

        $restoredTrainees = DB::connection('test_restore')->table('trainees')->get();
        $this->assertCount(2, $restoredTrainees);
        $this->assertEquals('كابتن محمود شلتوت', $restoredTrainees[0]->name);
        $this->assertEquals(85.5, (float) $restoredTrainees[0]->weight);
        $this->assertNull($restoredTrainees[1]->notes);

        $restoredPlans = DB::connection('test_restore')->table('workout_plans')->get();
        $this->assertCount(1, $restoredPlans);
        $this->assertEquals('خطة تضخيم العضلات - أسبوع 1', $restoredPlans[0]->title);
    }

    public function test_can_create_encrypted_backup_and_decrypt_successfully(): void
    {
        $key = DatabaseBackupService::generateEncryptionKey();

        $result = $this->service->createBackup([
            'connection' => 'test_source',
            'encrypt' => true,
            'encryption_key' => $key,
            'storage_path' => $this->testStorageDir,
        ]);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['encrypted']);
        $this->assertFileExists($result['file_path']);
        $this->assertStringEndsWith('.sql.gz.enc', $result['filename']);

        // Inspect raw file header to ensure it has our GCM container signature
        $handle = fopen($result['file_path'], 'rb');
        $magic = fread($handle, strlen(DatabaseBackupService::GCM_HEADER));
        fclose($handle);
        $this->assertEquals(DatabaseBackupService::GCM_HEADER, $magic);

        // Decrypt into a temporary gzip file and verify integrity
        $decryptedGz = $this->testStorageDir . '/decrypted_test.sql.gz';
        $binaryKey = $this->service->normalizeKey($key);
        $this->service->decryptBackupFile($result['file_path'], $decryptedGz, $binaryKey);

        $this->assertFileExists($decryptedGz);
        $this->assertTrue($this->service->verifyGzipIntegrity($decryptedGz));

        // Restore decrypted file
        $restoreResult = $this->service->restoreBackup($result['file_path'], 'test_restore', $key);
        $this->assertTrue($restoreResult['success']);

        $traineeCount = DB::connection('test_restore')->table('trainees')->count();
        $this->assertEquals(2, $traineeCount);
    }

    public function test_decryption_fails_with_invalid_key_or_corrupted_data(): void
    {
        $correctKey = DatabaseBackupService::generateEncryptionKey();
        $wrongKey = DatabaseBackupService::generateEncryptionKey();

        $result = $this->service->createBackup([
            'connection' => 'test_source',
            'encrypt' => true,
            'encryption_key' => $correctKey,
            'storage_path' => $this->testStorageDir,
        ]);

        $decryptedGz = $this->testStorageDir . '/failed_decrypt.sql.gz';
        $wrongBinaryKey = $this->service->normalizeKey($wrongKey);

        // Wrong key must throw RuntimeException
        $this->expectException(RuntimeException::class);
        $this->service->decryptBackupFile($result['file_path'], $decryptedGz, $wrongBinaryKey);
    }

    public function test_detection_of_corrupted_and_tampered_backup(): void
    {
        $key = DatabaseBackupService::generateEncryptionKey();
        $binaryKey = $this->service->normalizeKey($key);

        $result = $this->service->createBackup([
            'connection' => 'test_source',
            'encrypt' => true,
            'encryption_key' => $key,
            'storage_path' => $this->testStorageDir,
        ]);

        // Tamper with the ciphertext inside the file
        $fileBytes = file_get_contents($result['file_path']);
        // Flip bits in the middle of ciphertext
        $fileBytes[strlen($fileBytes) - 10] = chr(ord($fileBytes[strlen($fileBytes) - 10]) ^ 0xFF);

        $tamperedFile = $this->testStorageDir . '/tampered_backup.sql.gz.enc';
        file_put_contents($tamperedFile, $fileBytes);

        $decryptedGz = $this->testStorageDir . '/tampered_out.sql.gz';

        // Tampered authenticated cipher must fail tag verification
        $this->expectException(RuntimeException::class);
        $this->service->decryptBackupFile($tamperedFile, $decryptedGz, $binaryKey);
    }

    public function test_fails_when_encryption_enabled_without_encryption_key(): void
    {
        Config::set('backup.encryption_key', null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/BACKUP_ENCRYPTION_KEY/');

        $this->service->createBackup([
            'connection' => 'test_source',
            'encrypt' => true,
            'encryption_key' => null,
            'storage_path' => $this->testStorageDir,
        ]);
    }

    public function test_retention_policy_prunes_older_backups_and_preserves_unrelated_files(): void
    {
        $this->service->ensureSafeStorageDirectory($this->testStorageDir);

        // Create 6 dummy backup files with staggered timestamps
        $createdFiles = [];
        for ($i = 1; $i <= 6; $i++) {
            $filename = "backup-gym-2026-10-0{$i}_000000.sql.gz";
            $filePath = $this->testStorageDir . '/' . $filename;
            file_put_contents($filePath, 'dummy_gz_content');
            touch($filePath, time() - (7 - $i) * 86400); // Day 1 to Day 6
            $createdFiles[] = $filePath;
        }

        // Create an unrelated file that must never be deleted
        $unrelatedFile = $this->testStorageDir . '/trainee_report.csv';
        file_put_contents($unrelatedFile, 'id,name\n1,test');

        // Retention count is 4: 6 backups exist, 2 oldest should be pruned
        $prunedCount = $this->service->pruneOldBackups($this->testStorageDir, 4);

        $this->assertEquals(2, $prunedCount);

        // The 2 oldest must be deleted
        $this->assertFileDoesNotExist($createdFiles[0]);
        $this->assertFileDoesNotExist($createdFiles[1]);

        // The 4 newest must be preserved
        $this->assertFileExists($createdFiles[2]);
        $this->assertFileExists($createdFiles[3]);
        $this->assertFileExists($createdFiles[4]);
        $this->assertFileExists($createdFiles[5]);

        // Unrelated file must be preserved
        $this->assertFileExists($unrelatedFile);
    }

    public function test_retention_policy_never_deletes_when_backups_less_than_or_equal_to_retention_count(): void
    {
        $this->service->ensureSafeStorageDirectory($this->testStorageDir);

        for ($i = 1; $i <= 3; $i++) {
            $filePath = $this->testStorageDir . "/backup-gym-2026-10-0{$i}_000000.sql.gz";
            file_put_contents($filePath, 'dummy');
            touch($filePath, time() - (4 - $i) * 86400);
        }

        $prunedCount = $this->service->pruneOldBackups($this->testStorageDir, 4);
        $this->assertEquals(0, $prunedCount);
    }

    public function test_aborts_if_storage_path_is_inside_public_web_root(): void
    {
        $insecurePath = public_path('insecure_backups');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/public web root/');

        $this->service->ensureSafeStorageDirectory($insecurePath);
    }

    public function test_handles_large_table_streaming_without_memory_exhaustion(): void
    {
        $pdo = DB::connection('test_source')->getPdo();

        // Insert 350 rows into trainees to test batch chunking
        $stmt = $pdo->prepare("INSERT INTO trainees (name, email, weight) VALUES (?, ?, ?)");
        for ($i = 3; $i <= 350; $i++) {
            $stmt->execute(["Trainee #{$i}", "t{$i}@gym.test", 70.0 + ($i % 20)]);
        }

        $result = $this->service->createBackup([
            'connection' => 'test_source',
            'encrypt' => false,
            'storage_path' => $this->testStorageDir,
            'chunk_size' => 100, // Small chunk size to force multiple streaming iterations
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals(351, $result['rows_count']); // 350 trainees + 1 workout_plan

        // Restore and verify count
        $this->service->restoreBackup($result['file_path'], 'test_restore');
        $count = DB::connection('test_restore')->table('trainees')->count();
        $this->assertEquals(350, $count);
    }

    public function test_artisan_command_executes_successfully(): void
    {
        Config::set('database.default', 'test_source');

        $exitCode = Artisan::call('app:backup-database-weekly', [
            '--connection' => 'test_source',
            '--no-encrypt' => true,
        ]);

        $output = Artisan::output();

        $this->assertEquals(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Backup completed successfully!', $output);
        $this->assertStringContainsString('test_source', $output);
    }

    public function test_artisan_command_returns_failure_code_on_invalid_connection(): void
    {
        $exitCode = Artisan::call('app:backup-database-weekly', [
            '--connection' => 'invalid_missing_connection',
            '--no-encrypt' => true,
        ]);

        $this->assertEquals(Command::FAILURE, $exitCode);
    }
}

