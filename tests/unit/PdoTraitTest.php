<?php //tests/unit/PdoTraitTest.php

/*
vendor/bin/phpunit tests/unit/PdoTraitTest

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/unit/PdoTraitTest

can be used in any class
options:
1 connection is already eastablished, pass the PDO object
2 new connection
    A pass host, user, password,dbName
    B pass host, user, password - for migrations where we don't have
    dbName set yet
    c pass an array with keys: host, user, pass, dbName

 */

use PHPUnitTests\TestCase;
use Tesis\DBLayer\Traits\ConnectDbTrait;
use Tesis\DBLayer\Traits\PDOTrait;

require_once __DIR__ . '/../../src/traits/PDOTrait.php';
require_once __DIR__ . '/../../src/traits/ConnectDbTrait.php';

class PdoTraitTest extends TestCase
{
	use ConnectDbTrait, PDOTrait;

    public $class;
	public $conn;
	public $database;
	public $sql;
	public $error;
	public $fields;
	public $values;
	public $tableName;
	public $tableFields = [];
	public $tablePK;
	public $tableAI;
	public $tableNull = [];
	public $data = [];

	public function setUp()
    {
		parent::setUp();
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
            'dbName' => 'test_db',
            'dbCharset' => 'utf8'
        ];
    }

    /**
     * setDbName
     */
    protected function setDbName ()
    {
        return 'test_db';
    }

    // Clean up the the objects against which you tested
    public function tearDown()
    {

    }

    /**
     * test_open_connection
     */
    public function test_open_connection_db_obj ()
    {
        $this->output(__METHOD__);

        $this->dbDriver = 'PDO';
        $config = $this->setupConfig();

        $dns = 'mysql:host=' . $config['dbHost'] . ';dbname=' . $config['dbName'];

        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            //  Some drivers do not support native prepared statements or have limited support for them
            // When you switch databases like that it appears to force PDO to emulate prepared statements. Setting PDO::ATTR_EMULATE_PREPARES to false and then trying to use another database will fail.
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $config['dbCharset'],
        ];

        $conn = new PDO($dns, $config['dbUser'], $config['dbPass'], $options);

        $this->openConnection($conn);

        $this->assertNotNull($this->conn);
        $this->assertInternalType('object', $this->conn);
        $this->assertEquals('PDO', get_class($this->conn));

        $this->assertNotNull($this->conn);
        $this->assertNull($this->database);
        $this->assertInternalType('object', $this->conn);
        $this->assertEquals('PDO', get_class($this->conn));

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_open_connection_array_args
     */
    public function test_open_connection_array_args ()
    {
        $this->output(__METHOD__);

        $config = $this->setupConfig();

        $this->openConnection($config);

        $this->assertNotNull($this->conn);
        $this->assertNotNull($this->database);
        $this->assertInternalType('object', $this->conn);
        $this->assertEquals('PDO', get_class($this->conn));

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_open_connection_new
     */
    public function test_open_connection_new ()
    {
        $this->output(__METHOD__);

        $config = $this->setupConfig();
        $this->openConnection($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName'], $config['dbCharset']);

        $this->assertNotNull($this->conn);
        $this->assertNotNull($this->database);
        $this->assertInternalType('object', $this->conn);
        $this->assertEquals('PDO', get_class($this->conn));

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_open_connection_new_set_dbName
     * Error: PDO server error: SQLSTATE[HY000] [1049] Unknown database 'test_db1'
     */
    public function test_open_connection_new_set_dbName ()
    {
        $this->output(__METHOD__);

        $config = [
            'dbHost' => 'localhost',
            'dbUser' => 'root',
            'dbPass' => 'tesi',
            'dbName' => 'test_db1',
        ];

        $this->openConnection($config);

        $this->assertNotEmpty($this->error);
        $this->assertNull($this->conn);
        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_conn_fails
     * Error: Connection not established. Host, or config data are missing
     */
    public function test_conn_fails ()
    {
        $this->output(__METHOD__);

        $config = null;

        $this->openConnection();

        $this->assertNotEmpty($this->error);
        $this->assertNull($this->conn);

        $this->assertTrue(true, 'Expected Pass');
    }

/*---------------- End of class ----------------------*/
}