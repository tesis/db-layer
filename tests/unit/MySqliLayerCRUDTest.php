<?php //tests/unit/MySqliLayerCRUDTest.php

/*
vendor/bin/phpunit tests/unit/MySqliLayerCRUDTest

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/unit/MySqliLayerCRUDTest
 */

use PHPUnitTests\TestCase;
use Tesis\DBLayer\Database\MySqliLayer;

require_once __DIR__ . '/../../src/database/MysqliLayer.php';

class MySqliLayerCRUDTest extends TestCase
{

    public $class;
    public $data;

    public function setUp()
    {
        parent::setUp();

        $this->data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => 'data_created',
        ];

        $this->config = [
            'dbHost' => 'localhost',
            'dbUser' => 'root',
            'dbPass' => 'tesi',
            'dbName' => 'test_db',
            'dbCharset' => 'utf8',
        ];
    }

    // Clean up the the objects against which you tested
    public function tearDown()
    {

    }

    /**
     * init example of db connection
     */
    protected function init()
    {

        $this->class = new MysqliLayer($this->config);

        $this->class->tableName = 'users';

        $this->class->tablePK = $this->tableAI = 'uid';
        $this->class->tableNull = [];
        $this->class->tableFields = [
            'uid',
            'name',
            'email',
            'phone',
            'password',
            'address',
            'city',
            'created',
        ];
    }

    /**
     * delete_data
     */
    protected function delete_data ()
    {
        $this->init();
        // Delete record
        $sql = 'DELETE from users WHERE uid = 1';
        $this->class->conn->query($sql);
    }
    /**
     * test_init_class_conn_established
     */
    public function test_init_class_conn_established()
    {
        $this->output(__METHOD__);

        $this->init();

        $this->assertNotEmpty($this->class);
        $this->assertNotEmpty($this->class->conn);
        $this->assertEmpty($this->class->conn->error);
        $this->assertNotEmpty($this->class->database);
    }

    /**
     * test_insert_missing_fields
     * class error: Missing required fields: password, address, city
     */
    public function test_insert_missing_fields ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'created' => 'data_created',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users');
        $this->assertNotEmpty($class->tableFields);
        $this->assertEquals(1, $class->validate);

        $class->table('users', false);
        $this->assertEquals(false, $class->validate);

        $class->table('users')->insert($data);
        // Missing required fields: password, address, city
        $this->assertNotEmpty($class->error);
        $this->assertNotEmpty($class->errorInfo);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_insert_wrong_date
     * Incorrect datetime value: 'data_created' for column 'created' at row 1
     */
    public function test_insert_wrong_date ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => 'data_created',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users');
        $this->assertNotEmpty($class->tableFields);
        $this->assertEquals(1, $class->validate);

        $class->table('users', false);
        $this->assertEquals(false, $class->validate);

        $class->table('users')->insert($data);
        // else: Incorrect datetime value: 'data_created' for column 'created' at row 1
        $this->assertNotEmpty($class->error);
        $this->assertNotEmpty($class->errorInfo);

        // print_r($class);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_update_pass_pk_not_exists
     */
    public function test_update_pass_pk_not_exists ()
    {
        $this->output(__METHOD__);

        $data = [
            // 'uid' => 1,
            'name' => 'tereza22',
            'email' => 'email',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users')->update($data);
        $this->assertNotEmpty($class->error);
        $this->assertNotEmpty($class->errorInfo);
        $this->assertEquals(0, $class->affectedRows, 'Pass: query performed');
    }

    /**
     * test_update_pass_record_not_exists
     */
    public function test_update_pass_record_not_exists ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza22',
            'email' => 'email',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users')->update($data);

        $this->assertEmpty($class->error);
        $this->assertEmpty($class->errorInfo);
        $this->assertEquals(0, $class->affectedRows, 'Pass: query performed');
    }

    /**
     * test_insert_pass
     */
    public function test_insert_pass ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-1-1',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users')->insert($data);

        $this->assertEmpty($class->error);
        $this->assertEmpty($class->errorInfo);
    }

    /**
     * test_insert_duplicate_fail
     * Duplicate entry '1' for key 'PRIMARY'
     */
    public function test_insert_duplicate_fail ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-1-1',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users')->insert($data);

        $this->assertNotEmpty($class->error);
        $this->assertNotEmpty($class->errorInfo);
    }

    /**
     * test_update_fail
     */
    public function test_update_fail()
    {
        $this->output(__METHOD__);

        $this->init();

        $data = [
            'uid' => 2,
            'name' => 'tereza51',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2009-1-1',
        ];

        $this->class->update($data);
        echo $this->class->error;
        $this->assertEquals(null, $this->class->affectedRows, 'Pass: query failed');
    }

    /**
     * test_update_pass_first
     */
    public function test_update_pass_first ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza22',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-1-1',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users')->update($data);

        $this->assertEmpty($class->error);
        $this->assertEmpty($class->errorInfo);
        $this->assertEquals(1, $class->affectedRows, 'Pass: query performed');
    }

    /**
     * test_update_pass_next
     */
    public function test_update_pass_next ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza22',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-1-1',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users')->update($data);

        $this->assertEmpty($class->error);
        $this->assertEmpty($class->errorInfo);
        $this->assertEquals(0, $class->affectedRows, 'Pass: query performed');
    }

    /**
     * test_delete_no_id
     */
    public function test_delete_no_id ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza22',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-1-1',
        ];
        $class = new MysqliLayer($this->config);

        // .ID is required
        $class->table('users')->delete();

        $this->assertNotEmpty($class->error);
        $this->assertNotEmpty($class->errorInfo);
        $this->assertEquals(0, $class->affectedRows, 'Pass: query performed');
        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_delete_pass_record_not_exists
     * affectedRows = 0
     */
    public function test_delete_pass_record_not_exists ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza22',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-1-1',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users')->delete(2);

        $this->assertEmpty($class->error);
        $this->assertEmpty($class->errorInfo);
        $this->assertEquals(0, $class->affectedRows, 'Pass: query performed');
    }

    /**
     * test_delete_pass
     */
    public function test_delete_pass ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza22',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-1-1',
        ];
        $class = new MysqliLayer($this->config);

        $class->table('users')->delete(1);

        $this->assertEmpty($class->error);
        $this->assertEmpty($class->errorInfo);
        $this->assertEquals(1, $class->affectedRows, 'Pass: query performed');
    }


/*---------------- End of class ----------------------*/
}
