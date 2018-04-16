<?php //tests/unit/MySqliTraitTest.php

/*
vendor/bin/phpunit tests/unit/MySqliTraitTest

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/unit/MySqliTraitTest

can be used in any class
options:
1 connection is already eastablished, pass the mysqli object
2 new connection
    A pass host, user, password,dbName
    B pass host, user, password - for migrations where we don't have
    dbName set yet
    c pass an array with keys: host, user, pass, dbName

 */

use PHPUnitTests\TestCase;
use Tesis\DBLayer\Traits\ConnectDbTrait;
use Tesis\DBLayer\Traits\MySqliTrait;

require_once __DIR__ . '/../../src/traits/MySqliTrait.php';
require_once __DIR__ . '/../../src/traits/ConnectDbTrait.php';

class MySqliTraitTest extends TestCase
{
	use ConnectDbTrait, MySqliTrait;

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
     * setupConn
     */
    protected function setupConn ()
    {
        $config = $this->setupConfig();
        $this->dbDriver = 'mysqli';

        return new mysqli($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName']);
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
     * test_open_connection_mysqli_obj
     */
    public function test_open_connection_mysqli_obj ()
    {
        $this->output(__METHOD__);

        $conn = $this->setupConn();

        $this->openConnection($conn);

        $this->assertNotNull($this->conn);
        $this->assertInternalType('object', $this->conn);
        $this->assertEquals('mysqli', get_class($this->conn));

        $this->assertNotNull($this->conn);
        $this->assertNull($this->database);
        $this->assertInternalType('object', $this->conn);
        $this->assertEquals('mysqli', get_class($this->conn));

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
        $this->assertEquals('mysqli', get_class($this->conn));

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_open_connection_new
     */
    public function test_open_connection_new ()
    {
        $this->output(__METHOD__);

        $config = $this->setupConfig();
        $this->openConnection($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName']);

        $this->assertNotNull($this->conn);
        $this->assertNotNull($this->database);
        $this->assertInternalType('object', $this->conn);
        $this->assertEquals('mysqli', get_class($this->conn));

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_open_connection_new_no_dbName
     */
    public function test_open_connection_new_no_dbName ()
    {
        $this->output(__METHOD__);

        $config = $this->setupConfig();
        unset($config['dbName']);
        $this->openConnection($config['dbHost'], $config['dbUser'], $config['dbPass']);

        $this->assertNotNull($this->conn);
        $this->assertNull($this->database);
        $this->assertInternalType('object', $this->conn);
        $this->assertEquals('mysqli', get_class($this->conn));

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_open_connection_new_no_dbName_fail
     */
    public function test_open_connection_empty_array_fail ()
    {
        $this->output(__METHOD__);

        $config = [];
        $this->openConnection($config);

        $this->assertNull($this->conn);
        $this->assertNotEmpty($this->error);
        $this->assertNotEmpty($this->errorInfo);

        $config = null;
        $this->openConnection($config);

        $this->assertNull($this->conn);
        $this->assertNotEmpty($this->error);
        $this->assertNotEmpty($this->errorInfo);

        $config = '';
        $this->openConnection($config);

        $this->assertNull($this->conn);
        $this->assertNotEmpty($this->error);
        $this->assertNotEmpty($this->errorInfo);

        $config = ['sss', 'dsd', 'dfsd'];
        $this->openConnection($config);

        print_r($this->error);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_open_connection_new_set_dbName
     */
    public function test_open_connection_new_set_dbName ()
    {
        $this->output(__METHOD__);

        $config = $this->setupConfig();
        unset($config['dbName']);
        $this->openConnection($config['dbHost'], $config['dbUser'], $config['dbPass']);

        $this->assertNotNull($this->conn);
        $this->assertNull($this->database);
        $this->assertInternalType('object', $this->conn);
        $this->assertEquals('mysqli', get_class($this->conn));

        $this->setDatabase($this->setDbName());
        $this->assertNotNull($this->database);

        $this->assertTrue(true, 'Expected Pass');
    }

/*---------------- End of class ----------------------*/
}