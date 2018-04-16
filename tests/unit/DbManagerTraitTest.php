<?php //tests/unit/DbManagerTraitTest.php

/*
vendor/bin/phpunit tests/unit/DbManagerTraitTest

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/unit/DbManagerTraitTest
 */

use PHPUnitTests\TestCase;
use Tesis\DBLayer\Traits\DbManagerTrait;

require_once __DIR__ . '/../../src/traits/DbManagerTrait.php';

class DbManagerTraitTest extends TestCase
{
	use DbManagerTrait;

	public $class;
	public $conn;
	public $database;
	public $sql;
	public $error;
    public $migrationFile;
    public $seederFile;
    public $lastQuery;

	public function setUp()
    {
		parent::setUp();
        $this->migrationFile = __DIR__ . '/../../database/migrations/migrations.php';
        $this->seederFile = __DIR__ . '/../../database/seeders/seeders.php';
	}

    public function tearDown()
    {
	   // Clean up the the objects against which you tested
	}

    /**
     * setupConfig
     */
    protected function setupConfig ()
    {
        return [
            'dbHost' => 'localhost',
            'dbUser' => 'root',
            'dbPass' => 'tesi',
            'dbName' => 'angular',
            'dbCharset' => 'UTF-8'
        ];
    }

    /**
     * setupConn
     */
    protected function setupConn ()
    {
        $config = $this->setupConfig();

        return new mysqli($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName']);
    }

    /**
     * selectDatabase for mysqli
     */
    protected function selectDatabase ($dbName)
    {
        $this->conn->select_db($dbName);
        $this->database = $dbName;
    }

	/**
     * init example of db connection
     */
    protected function init() {
        $this->conn = $this->setupConn();
    }
    /**
     * test_createDatabase_not_connected_fail
     */
    public function test_createDatabase_not_connected_fail ()
    {
        $this->output(__METHOD__);

        $test = $this->createDatabase('xxx');

        $this->assertEquals('Tesis\DBLayer\Traits\DbManagerTrait::createDatabase: Connection not established', $this->error);
        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_createDatabase_dbName_missing_fail
     */
    public function test_createDatabase_dbName_missing_fail ()
    {
        $this->output(__METHOD__);

        $this->init();

        $test = $this->createDatabase();
        $this->assertEquals('Tesis\DBLayer\Traits\DbManagerTrait::createDatabase: Database name is missing', $this->error);
    }

    /**
     * test_dropDatabase_not_connected_fail
     */
    public function test_dropDatabase_not_connected_fail ()
    {
        $this->output(__METHOD__);

        $this->dropDatabase('xxx');
        $this->assertEquals('Tesis\DBLayer\Traits\DbManagerTrait::dropDatabase: Connection not established', $this->error);

        $this->assertTrue(true);
    }

    /**
     * test_dropDatabase_dbName_missing_fail
     */
    public function test_dropDatabase_dbName_missing_fail ()
    {
        $this->output(__METHOD__);

        $this->init();

        $this->dropDatabase();

        $this->assertEquals('Tesis\DBLayer\Traits\DbManagerTrait::dropDatabase: Database name is missing', $this->error);
    }

    /**
     * test_createDatabase_db_exists_pass
     */
    public function test_createDatabase_pass ()
    {
        $this->output(__METHOD__);

        $this->init();
        $test = $this->createDatabase('xxx');

        $this->assertTrue($test, 'Expected Pass');
    }
    /**
     * test_createDatabase_db_exists_pass
     */
    public function test_createDatabase_db_duplicated_pass ()
    {
        $this->output(__METHOD__);

        $this->init();
        $test = $this->createDatabase('xxx');
        $this->assertEquals('Database already created: xxx', $this->error);
    }

    /**
     * test_database_exists_fail
     */
    public function test_database_exists_fail ()
    {
        $this->output(__METHOD__);

        $this->init();
        $test = $this->checkDatabaseExists('yyy');

        $this->assertNotNull($test, 'Expected Pass');
    }

    /**
     * test_database_exists_pass
     */
    public function test_database_exists_pass ()
    {
        $this->output(__METHOD__);

        $this->init();
        $test = $this->checkDatabaseExists('xxx');

        $this->assertNotNull($test, 'Expected Pass');
    }

    /**
     * test_migrate_pass
     */
    public function test_migrate_pass ()
    {
        $this->output(__METHOD__);

        $this->init();
        $this->selectDatabase('xxx');

        // Migrations
        $this->migrate($this->migrationFile);

        $this->assertEquals('xxx', $this->database);
        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_seed_pass
     */
    public function test_seed_pass ()
    {
        $this->output(__METHOD__);

        $this->init();
        $this->selectDatabase('xxx');

        // Seeders
        $this->seed($this->seederFile);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_dropDatabase_fail
     */
    public function test_dropDatabase_fail ()
    {
        $this->output(__METHOD__);

        $this->init();
        $test = $this->dropDatabase('yyy');
        $this->assertEquals("Can't drop database 'yyy'; database doesn't exist", $this->conn->error);
        $this->assertNull($this->error);
    }

    /**
     * test_dropDatabase_pass
     */
    public function test_dropDatabase_pass ()
    {
        $this->output(__METHOD__);

        $this->init();
        $test = $this->dropDatabase('xxx');

        $this->assertEmpty($this->conn->error, 'Expected Pass');
    }


/*---------------- End of class ----------------------*/
}