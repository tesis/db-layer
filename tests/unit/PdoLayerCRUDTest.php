<?php //tests/unit/PdoLayerCRUDTest.php

/*
vendor/bin/phpunit tests/unit/PdoLayerCRUDTest

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/unit/PdoLayerCRUDTest
 */

use PHPUnitTests\TestCase;
use Tesis\DBLayer\Database\PDOLayer;

require_once __DIR__ . '/../../src/database/PDOLayer.php';

class PdoLayerCRUDTest extends TestCase
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
        $config = [
            'dbHost' => 'localhost',
            'dbUser' => 'root',
            'dbPass' => 'tesi',
            'dbName' => 'test_db',
            'dbCharset' => 'utf8',
            'MAPPER' => '',
            'CONTACT_EMAIL' => 'tereza.simcic@gmail.com',
            'ENVIRONMENT' => 'dev',
            'DEBUG' => true
        ];
        $this->class = new PDOLayer($config);

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
     * test_init_class_conn_established
     */
    public function test_init_class_conn_established()
    {
        $this->output(__METHOD__);

        $this->init();

        $this->assertNotEmpty($this->class);
        $this->assertNotEmpty($this->class->conn);
        $this->assertEmpty($this->class->error);
        $this->assertNotEmpty($this->class->database);
        $this->assertTrue(true);
    }

    /**
     * test_insert_fail
     * Error: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: '' for column 'created' at row 1
     */
    public function test_insert_fail()
    {
        $this->output(__METHOD__);

        $this->init();

        $test = $this->class->table('users')->insert($this->data, true);

        $this->assertNotEmpty($this->class->error);
        $this->assertEquals('INSERT INTO users (`uid`,`name`,`email`,`phone`,`password`,`address`,`city`,`created`) VALUES (:uid,:name,:email,:phone,:password,:address,:city,:created)', $this->class->sql);
        $this->assertTrue(true);
    }

    /**
     * test_insert_fail_missing_field
     * Error: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: '' for column 'created' at row 1
     */
    public function test_insert_fail_missing_field ()
    {
        $this->output(__METHOD__);

        $this->data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city'
        ];
        $this->init();

        $test = $this->class->table('users')->insert($this->data, true);
        // echo $this->class->error;
        $this->assertNotEmpty($this->class->error, "Pass: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: '' for column 'created' at row 1");
    }

    /**
     * test_insert_fail_missing_fields: missing more than one field
     * Error: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: '' for column 'created' at row 1
     */
    public function test_insert_fail_missing_fields ()
    {
        $this->output(__METHOD__);

        $this->data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
        ];
        $this->init();

        $test = $this->class->table('users')->insert($this->data, true);
        // echo $this->class->error;
        $this->assertNotEmpty($this->class->error, "Pass: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: '' for column 'created' at row 1");;
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
            'created' => '2009-1-1',
        ];
        $this->init();

        $test = $this->class->table('users')->insert($data, true);
        // echo $this->class->sql;
        // echo $this->class->error;
        $this->assertEmpty($this->class->error);
        $this->assertEquals('INSERT INTO users (`uid`,`name`,`email`,`phone`,`password`,`address`,`city`,`created`) VALUES (:uid,:name,:email,:phone,:password,:address,:city,:created)', $this->class->sql);
        $this->assertTrue(true);

        // Delete record
        // $sql = 'DELETE from users WHERE uid = 1';
        // $this->class->conn->query($sql);
    }

    /**
     * test_insert_duplicated_id_fail
     * Error: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1' for key 'PRIMARY'
     */
    public function test_insert_duplicated_id_fail()
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
            'created' => '2009-1-1',
        ];

        $this->init();

        $insert = $this->class->table('users')->insert($data, true);

        $this->assertNotEmpty($this->class->error);
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
     * test_update_pass
     */
    public function test_update_pass()
    {
        $this->output(__METHOD__);

        $this->init();

        $data = [
            'uid' => 1,
            'name' => 'tereza51',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2009-1-1',
        ];

        $this->class->update($data);

        $this->assertEquals(0, $this->class->affectedRows, 'Pass: query performed');
    }

    /**
     * test_delete
     */
    public function test_delete()
    {
        $this->output(__METHOD__);

        $this->init();

        try {
            $this->class->delete(1, 'uid');
        } catch (Exception $e) {
            echo 'ERROR: ' . $e->getMessage . EOL;
        }

        $this->assertEmpty($this->class->error);
    }

    /**
     * test_readFromDb
     */
    public function test_readFromDb()
    {
        $this->output(__METHOD__);

        $this->init();

        $this->class->readFromDb('users');

        $this->assertNotEmpty($this->class->tableMapper);
        $this->assertTrue(true, 'Pass');
    }

    /**
     * test_select_validate
     */
    public function atest_customInsert ()
    {
        $this->output(__METHOD__);

        $this->init();

        $data = [
            'uid' => 5,// no effect
            'name' => 'terezapdo',
            'email' => 'email',
            'address' => 'address',
        ];

        // This can be used only for simple tables, with no unique properties
        // and is not recommended
        $this->class->table('users', false)->insertCustom($data);

        $this->assertEmpty($this->class->error);
        $this->assertTrue(true, 'Expected Pass');
    }


/*---------------- End of class ----------------------*/
}
