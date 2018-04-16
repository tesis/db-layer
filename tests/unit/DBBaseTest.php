<?php //tests/unit/DBBaseTest.php

/*
vendor/bin/phpunit tests/unit/DBBaseTest

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/unit/DBBaseTest
 */

use PHPUnitTests\TestCase;
use Tesis\DBLayer\Database\DBBase;
use Tesis\DBLayer\Database\MysqliLayer;

require_once __DIR__ . '/../../src/database/DBBase.php';
require_once __DIR__ . '/../../src/database/MysqliLayer.php';

class DBBaseTest extends TestCase
{

    public $class;
    public $data;
    public $config;

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
     * test_prepare_insert_fields_values
     */
    public function test_prepare_insert_fields_values ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'password' => 'password',
        ];
        $class = new MysqliLayer($this->config);
        $class->tableFields = ['uid', 'name', 'email', 'phone', 'address'];
        $class->tableNull = ['phone', 'address'];
        $this->invoke_nonpublic_method($class, 'prepareInsertFieldsValues', [$data, false]);
        $this->invoke_nonpublic_method($class, 'validateInsertFields', []);

        $this->assertGreaterThan(1, count($class->fields));
        $this->assertGreaterThan(1, count($class->values));

    }

    /**
     * test_prepare_insert_fields_values_table_fileds_null
     */
    public function test_prepare_insert_fields_values_table_fileds_null ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
        ];
        $class = new MysqliLayer($this->config);
        $class->tableFields = ['uid', 'name', 'email', 'phone', 'address'];
        $this->invoke_nonpublic_method($class, 'prepareInsertFieldsValues', [$data, false]);
        $this->invoke_nonpublic_method($class, 'validateInsertFields', []);

        $this->assertGreaterThan(3, count($class->fields));
        $this->assertGreaterThan(3, count($class->values));
        $this->assertTrue(true, 'Expected Pass');

    }

    /**
     * test_prepare_insert_fields_values_fail tableFields should be already set
     * @expectedException Exception
     */
    public function test_prepare_insert_fields_values_fail ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'address' => 'address',
        ];
        $class = new MysqliLayer($this->config);
        $class->tableFields = ['uid', 'name', 'email', 'phone', 'address'];
        $class->tableNull = [];
        $this->invoke_nonpublic_method($class, 'prepareInsertFieldsValues', [$data, false]);
        $this->invoke_nonpublic_method($class, 'validateInsertFields', []);
    }

    /**
     * test_table
     * @expectedException InvalidArgumentException
     */
    public function test_table ()
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

        $class->table()->insert($data);

        $this->assertTrue(true, 'Expected Pass');
    }


    /**
     * test_table_empty_exception
     */
    public function test_table_empty_exception ()
    {
        $this->output(__METHOD__);

        $class = new MysqliLayer($this->config);
        // Catch table exception
        try {
            $class->table()->insert($data);
        } catch (InvalidArgumentException $e) {
            echo 'invalid arg: ' . $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_cannot_read_mapper
     * DBBase::table set reader to F,
     * output will be: Cannot read mapper
     */
    public function test_cannot_read_mapper ()
    {
        $this->output(__METHOD__);

        $class = new MysqliLayer($this->config);
        // Catch table exception
        try {
            $class->table('users');
        } catch (InvalidArgumentException $e) {
            echo 'invalid arg: ' . $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_prepare_update_fields_values_fail tableFields should be already set
     * @expectedException Exception
     * Table fields / PK are empty
     */
    public function test_prepare_update_fields_values_fail ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'address' => 'address',
        ];
        $class = new MysqliLayer($this->config);
        $class->tableFields = ['uid', 'name', 'email', 'phone', 'address'];
        $class->tableNull = [];
        $this->invoke_nonpublic_method($class, 'prepareUpdateFieldsValues', [$data, false]);
    }

    /**
     * test_prepare_update_fields_values_pass
     */
    public function test_prepare_update_fields_values_pass ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'address' => 'address',
        ];
        $class = new MysqliLayer($this->config);
        $class->tableFields = ['uid', 'name', 'email', 'phone', 'address'];
        $class->tablePK = 'uid';
        $class->tableNull = [];
        $this->invoke_nonpublic_method($class, 'prepareUpdateFieldsValues', [$data, false]);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_selects
     */
    public function test_selects ()
    {
        $this->output(__METHOD__);

        $class = new MysqliLayer($this->config);
        // Catch table exception
        try {
            $class->table('users');
        } catch (InvalidArgumentException $e) {
            echo 'invalid arg: ' . $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_select_validate
     */
    public function test_customInsert ()
    {
        $this->output(__METHOD__);

        $data = [
            'uid' => 5,// no effect
            'name' => 'tereza',
            'email' => 'email',
            'address' => 'address',
        ];
        $class = new MysqliLayer($this->config);

        // This can be used only for simple tables, with no unique properties
        // and is not recommended
        $class->table('users', false)->insertCustom($data);

        $this->assertEmpty($class->error);
        $this->assertTrue(true, 'Expected Pass');
    }

/*---------------- End of class ----------------------*/
}
