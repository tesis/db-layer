<?php //tests/unit/PdoLayerSelectTest.php

/*
vendor/bin/phpunit tests/unit/PdoLayerSelectTest

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/unit/PdoLayerSelectTest
 */
// REDO tests for insert
use PHPUnitTests\TestCase;
use Tesis\DBLayer\Database\PDOLayer;

require_once __DIR__ . '/../../src/database/PDOLayer.php';

class PdoLayerSelectTest extends TestCase
{

    public $class;
    // public $conn;
    // public $database;
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
        ];
        $this->class = new PDOLayer($config);

        // $this->class->tableName = 'users';

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
        $this->assertEquals('00000', $this->class->conn->errorCode());
        $this->assertEquals('00000', $this->class->conn->errorInfo()[0]);
        $this->assertEmpty($this->class->conn->errorInfo()[1]);
        $this->assertEmpty($this->class->conn->errorInfo()[2]);
        $this->assertNotEmpty($this->class->database);
    }

    /**
     * test_get_simple
     *
     * @dataProvider sql_data_provider_simple_where
     */
    public function test_get_simple_where ($fields, $where1, $where2, $expected)
    {
        $this->output(__METHOD__);

        $this->init();

        $result = $this->sql_data_provider_simple_where();

        $test = $this->class->table('users')->select($fields)->where($where1, $where2)->get();

        $this->assertEquals($expected, trim($this->class->sql));

    }

    /**
     * sql_data_provider_simple_where
     */
    public function sql_data_provider_simple_where ()
    {
        return [
            ['uid, name, address', 'uid' , 169 , 'SELECT uid,name,address FROM users WHERE uid = :uid'],
            ['uid, name, address, test', 'uid' , 169 , 'SELECT uid,name,address FROM users WHERE uid = :uid', 'Pass: not existing field ignored'],
            ['uid, name, address, test', 'uid' , 2 , 'SELECT uid,name,address FROM users WHERE uid = :uid', 'Pass: id not exists'],
            ['uid, name, address, test', 'name' , 'tereza' , 'SELECT uid,name,address FROM users WHERE name = :name', 'Pass: name exists'],
        ];
    }

    /**
     * test_get_simple_where_or
     *
     * @dataProvider sql_data_provider_simple_where_or
     */
    public function test_get_simple_where_or ($fields, $where1, $where2, $whereOr,$expected)
    {
        $this->output(__METHOD__);

        $this->init();

        $result = $this->sql_data_provider_simple_where_or();

        $test = $this->class->table('users')->select($fields)->where($where1, $where2)->orWhere($where1, $whereOr)->get();

        $this->assertEquals($expected, trim($this->class->sql));

    }

    /**
     * sql_data_provider_simple_where_or
     */
    public function sql_data_provider_simple_where_or ()
    {
        return [
            ['uid, name, address', 'uid' , 169 , 170, 'SELECT uid,name,address FROM users WHERE uid = :uid OR uid = :uid'],
            ['uid, name, address, test', 'uid' , 169 , 170, 'SELECT uid,name,address FROM users WHERE uid = :uid OR uid = :uid', 'Pass: not existing field ignored'],
            ['uid, name, address, test', 'uid' , 2 , 3, 'SELECT uid,name,address FROM users WHERE uid = :uid OR uid = :uid', 'Pass: id not exists'],
        ];
    }

    /**
     * test_get_simple_where_and
     *
     * @dataProvider sql_data_provider_simple_where_and
     */
    public function test_get_simple_where_and ($fields, $where1, $where2, $whereAnd,$expected)
    {
        $this->output(__METHOD__);

        $this->init();

        $result = $this->sql_data_provider_simple_where_and();

        $test = $this->class->table('users')->select($fields)->where($where1, $where2)->andWhere($where1, $whereAnd)->get();

        $this->assertEquals($expected, trim($this->class->sql));

    }

    /**
     * sql_data_provider_simple_where_and
     */
    public function sql_data_provider_simple_where_and ()
    {
        return [
            ['uid, name, address', 'uid' , 169 , 170, 'SELECT uid,name,address FROM users WHERE uid = :uid AND uid = :uid'],
            ['uid, name, address, test', 'uid' , 169 , 170, 'SELECT uid,name,address FROM users WHERE uid = :uid AND uid = :uid', 'Pass: not existing field ignored'],
            ['uid, name, address, test', 'uid' , 2 , 3, 'SELECT uid,name,address FROM users WHERE uid = :uid AND uid = :uid', 'Pass: id not exists'],
        ];
    }

    /**
     * test_get_orderBy_limit
     */
    public function test_get_orderBy_limit ()
    {
        $this->output(__METHOD__);

        $this->init();

        $fields = 'uid, name, address';

        $test = $this->class->table('users')->select($fields)->where('uid', 169)->orderBy('uid', 'ASC')->get();

        $this->assertEquals('SELECT uid,name,address FROM users WHERE uid = :uid ORDER BY uid ASC', trim($this->class->sql));

        $test = $this->class->table('users')->select($fields)->where('uid', 169)->orderBy('uid', 'ASC')->get(4);

        $this->assertEquals('SELECT uid,name,address FROM users WHERE uid = :uid ORDER BY uid ASC LIMIT 4', $this->class->sql);
    }

    /**
     * test_first
     */
    public function test_first ()
    {
        $this->output(__METHOD__);

        $this->init();

        $fields = 'uid, name, address';

        $test = $this->class->table('users')->select($fields)->where('uid', 169)->orderBy('uid', 'ASC')->first();

        $this->assertEquals('SELECT uid,name,address FROM users WHERE uid = :uid ORDER BY uid ASC LIMIT 1', $this->class->sql);

    }

    /**
     * test_all
     */
    public function test_all ()
    {
        $this->output(__METHOD__);

        $this->init();

        $fields = 'uid, name, address';

        $test = $this->class->table('users')->select($fields)->where('uid', 169)->orderBy('uid', 'ASC')->all();

        $this->assertEquals('SELECT uid,name,address FROM users WHERE uid = :uid ORDER BY uid ASC', $this->class->sql);

    }

    public function test_all_exec ()
    {
        $this->output(__METHOD__);

        $this->init();

        $fields = 'uid, name, address';

        $test = $this->class->table('users')->select($fields)->where('uid', 169, '<')->orderBy('uid', 'ASC')->all();

        $this->assertEquals('SELECT uid,name,address FROM users WHERE uid < :uid ORDER BY uid ASC', $this->class->sql);

        $test = $this->class->execute();

    }

    public function test_first_exec ()
    {
        $this->output(__METHOD__);

        $this->init();

        $fields = 'uid, name, address';

        $test = $this->class->table('users')->select($fields)->where('uid', 169, '<')->orderBy('uid', 'ASC')->first();

        $this->assertEquals('SELECT uid,name,address FROM users WHERE uid < :uid ORDER BY uid ASC LIMIT 1', $this->class->sql);

        $test = $this->class->execute();

    }

    public function test_get_no_where_exec ()
    {
        $this->output(__METHOD__);

        $this->init();

        $fields = 'uid, name, address';

        $test = $this->class->table('users')->select($fields)->get(2);

        $this->assertEquals('SELECT uid,name,address FROM users LIMIT 2', $this->class->sql);

        $test = $this->class->execute();

        $this->assertEquals(2, $this->class->affectedRows);
        $this->assertEquals(2, count($test));
    }

    public function test_get_limit_exec ()
    {
        $this->output(__METHOD__);

        $this->init();

        $fields = 'uid, name, address';

        $test = $this->class->table('users')->select($fields)->orderBy('uid', 'ASC')->get(4);

        $this->assertEquals('SELECT uid,name,address FROM users ORDER BY uid ASC LIMIT 4', $this->class->sql);

        $test = $this->class->execute();

        $this->assertEquals(2, $this->class->affectedRows);
        $this->assertEquals(2, count($test));
    }

    /**
     * test_executeSql
     */
    public function test_executeSql ()
    {
        $this->output(__METHOD__);

        $this->init();

        $sql = 'SELECT uid,name,address FROM users ORDER BY uid ASC LIMIT 4';

        $test = $this->class->executeSql($sql);
        $this->assertEquals(2, count($test));
    }

    /**
     * test_executeSql
     */
    public function test_join ()
    {
        $this->output(__METHOD__);

        $this->init();

        $fields = 'users.uid, users.name, users.address, user_data.cat_name, user_data.dog_name';

        $test = $this->class->table('users', false)->select($fields, false)->join('user_data', 'users.uid = user_data.user_id')->get();
        $this->assertEquals('SELECT users.uid, users.name, users.address, user_data.cat_name, user_data.dog_name FROM users JOIN user_data ON users.uid = user_data.user_id', $this->class->sql);
        $test = $this->class->execute();
        $this->assertEquals(2, count($test));
        $this->assertTrue(true);
    }

/*---------------- End of class ----------------------*/
}
