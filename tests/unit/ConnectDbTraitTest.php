<?php //tests/unit/ConnectDbTraitTest.php

/*
vendor/bin/phpunit tests/unit/ConnectDbTraitTest

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/unit/ConnectDbTraitTest
 */

use PHPUnitTests\TestCase;
use Tesis\DBLayer\Traits\MySqliTrait;
use Tesis\DBLayer\Traits\ConnectDbTrait;

require_once __DIR__ . '/../../src/traits/MySqliTrait.php';
require_once __DIR__ . '/../../src/traits/ConnectDbTrait.php';

// Throw mysqli_sql_exception for errors instead of warnings
// mysqli_report(MYSQLI_REPORT_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class ConnectDbTraitTest extends TestCase
{
	use ConnectDbTrait , MySqliTrait;

	public $class;
	public $conn;
	public $database;
	public $sql;
	public $error;
    public $lastQuery;
    public $line;
    public $file;

	public function setUp()
    {
		parent::setUp();
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
            'dbCharset' => 'utf8'
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
     * test_connect_pass
     */
    public function test_connect_pass ()
    {
        $this->output(__METHOD__);

        $this->assertTrue(true, 'Expected Pass');
        $conn = null;
        try {
            $conn = new mysqli('localhost', 'root', 'tesi', 'test_db');
            if($conn->connect_error) {
                // exception is caught within the function
                throw new mysqli_sql_exception( ' ERROR !!! ' . $conn->connect_error);
            }

        } catch(mysqli_sql_exception $e) {
            echo 'Server error mysqli: ' . $e->getMessage() .PHP_EOL;
            // return false - otherwise other actions will be performed
            // return false;
        }  catch(Exception $e) {
            echo 'Server error e: ' . $e->getMessage() .PHP_EOL;
            // return false;
        } finally {
            $this->conn = $conn;
        }
        $this->assertNotNull($this->conn);
        $this->assertNotEmpty($this->conn->client_info);
        $this->assertEmpty($this->conn->connect_errno);
        $this->assertEmpty($this->conn->connect_error);
    }

    /**
     * test_open_connection_credentials_pass
     */
    public function test_open_connection_credentials_pass ()
    {
        $this->output(__METHOD__);

        $this->openConnection('localhost', 'root', 'tesi', 'test_db');

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_open_connection_array_pass
     */
    public function test_open_connection_array_pass ()
    {
        $this->output(__METHOD__);

        $config = $this->setupConfig();
        $this->openConnection($config);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_open_connection_obj_pass
     */
    public function test_open_connection_obj_pass ()
    {
        $this->output(__METHOD__);

        $conn = new mysqli('localhost', 'root', 'tesi', 'test_db');
        $this->dbDriver = 'mysqli';
        $this->openConnection($conn);

        $this->assertNotNull($conn);
        $this->assertNotEmpty($this->dbDriver);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_set_database
     * expectedException Exception -caught
     * Tesis\DBLayer\Traits\ConnectDbTrait::setDatabase: Connection not established, check your credentials
     */
    public function test_set_database_conn_null ()
    {
        $this->output(__METHOD__);

        $this->setDatabase('test_db');

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_set_database
     * expectedException Exception - caught
     */
    public function test_set_database_dbName_null ()
    {
        $this->output(__METHOD__);

        $config = $this->setupConfig();
        $this->openConnection($config);

        $this->setDatabase();

        $this->assertTrue(true, 'Expected Pass');
    }


/*---------------- End of class ----------------------*/
}


