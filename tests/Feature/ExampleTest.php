<?php

namespace Tests\Feature;

use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Verify that the Laravel application boots properly in the testing environment.
     *
     * @return void
     */
    public function test_the_application_boots_in_testing_environment(): void
    {
        $this->assertNotNull($this->app, 'Application instance must be booted.');
        $this->assertTrue($this->app->isBooted(), 'Application must be marked as booted.');
        $this->assertSame('testing', app()->environment(), 'Application environment must be testing.');
    }

    /**
     * Verify that the test database connection is strictly isolated to SQLite in-memory
     * and does not fall back to or interact with the development database.
     *
     * @return void
     */
    public function test_the_database_is_isolated_to_sqlite_in_memory(): void
    {
        // 1. Verify default database configuration points to sqlite
        $defaultConnection = config('database.default');
        $this->assertSame('sqlite', $defaultConnection, 'Default database connection must be sqlite.');

        // 2. Verify active DB connection instance and driver
        $connection = DB::connection();
        $this->assertInstanceOf(SQLiteConnection::class, $connection, 'Connection must be an instance of SQLiteConnection.');
        $this->assertSame('sqlite', $connection->getName(), 'Connection name must be sqlite.');
        $this->assertSame('sqlite', $connection->getDriverName(), 'Driver name must be sqlite.');
        $this->assertSame(':memory:', $connection->getDatabaseName(), 'Database name must strictly be :memory:.');

        // 3. Verify SQLite configuration parameter matches :memory:
        $configuredDatabase = $connection->getConfig('database');
        $this->assertSame(':memory:', $configuredDatabase, 'Configured database parameter must strictly be :memory:.');
    }

    /**
     * Verify that the in-memory SQLite database can execute raw queries safely
     * and that it is completely isolated from existing application tables.
     *
     * @return void
     */
    public function test_the_isolated_in_memory_database_executes_queries_safely(): void
    {
        // Execute a query to verify active SQLite engine
        $versionResult = DB::select('SELECT sqlite_version() as version, 1 as is_isolated');
        $this->assertNotEmpty($versionResult, 'SQLite engine must return a valid query result.');
        $this->assertSame(1, (int) $versionResult[0]->is_isolated);
        $this->assertNotNull($versionResult[0]->version, 'SQLite version must be present.');

        // Verify that no application tables exist in this clean in-memory database
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $this->assertEmpty(
            $tables,
            'Fresh in-memory test database must not contain application tables from the development database.'
        );
    }
}
