<?php //tests/unit/SqlGeneratorReadTest.php

/*
vendor/bin/phpunit tests/unit/SqlGeneratorReadTest

alias phpunit='vendor/bin/phpunit --config phpunit.xml'
phpunit tests/unit/SqlGeneratorReadTest
*/

use PHPUnitTests\TestCase;
use Tesis\DBLayer\System\SqlGeneratorReader;
use Tesis\DBLayer\Traits\MySqliTrait;
// use Exception;
use Tesis\DbLayer\Exceptions\FileNotFoundException;
use Tesis\DbLayer\Exceptions\MapperNotValidException;
use Tesis\DbLayer\Exceptions\JsonNotValidException;
use Tesis\DBLayer\Exceptions\NotFoundException;

require_once(__DIR__ .'/../../src/system/SqlGeneratorReader.php');
require_once(__DIR__ .'/../../src/traits/MySqliTrait.php');
require_once(__DIR__ .'/../../src/exceptions/FileNotFoundException.php');
require_once(__DIR__ .'/../../src/exceptions/MapperNotValidException.php');
require_once(__DIR__ .'/../../src/exceptions/JsonNotValidException.php');
require_once(__DIR__ .'/../../src/exceptions/NotFoundException.php');

class SqlGeneratorReadTest extends TestCase
{
    public $class;
    public $filename;
    public $tableName;
    public $data;

    public function setUp()
    {
        parent::setUp();

        $this->tableName = 'users';
        $this->filename = 'tests/data/database/mappers/dbTablesMapper';

    }

    // Clean up the the objects against which you tested
    public function tearDown()
    {

    }

    /**
     * test_tableNameNotSet
     * @expectedException InvalidArgumentException
     */
    public function test_tableNameNotSet ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader();

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_tableNameNotEmpty
     * @expectedException InvalidArgumentException
     */
    public function test_tableNameNotEmpty ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader('');

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_tableNameNotEmpty
     * expectedException Tesis\DBLayer\Exceptions\NotFoundException - caught
     */
    public function test_tableNameNotExist ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader('blabla');

        $this->assertEmpty($reader->tableMapper);
        $this->assertTrue(true, 'Expected Pass');
    }

    public function test_tableNameExists ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader($this->tableName);

        $this->assertEquals('users', $this->get_nonpublic_property($reader, 'tableName'));

        $this->assertEquals('uid', $reader->tableMapper->PK);
        $this->assertEquals('uid', $reader->tableMapper->AI);
        $this->assertNotEmpty('uid', $reader->tableMapper->fields);
        $this->assertEquals('database/mappers/dbTablesMapper', $reader->filename);
        $this->assertTrue(true, 'Expected Pass, table properties defined');
    }

    /**
     * test_validateTableMapper_notValidJson
     *
     * @expectedException Tesis\DBLayer\Exceptions\JsonNotValidException
     * @expectedExceptionMessage Json not valid: Syntax error
     */
    public function test_validateTableMapper_notValidJson ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader($this->tableName);
$json = '{
    "meta": {
        "generated": "2018-04-04 07:28:42",
        "database": "DB_NAME"
    },
    "tables": {
        "user_data": {
            "fields": [
                "id",
                "user_id",
                "cat_name",
                "dog_name"
            ],
            "null": [],
            "PK": "id",
            "AI": "id"
        },
        "users": {
            "fields": [
                "uid",
                "name",
                "email",
                "phone",
                "password",
                "address",
                "city",
                "created"
            ],
            "null": [],
            "PK": "uid",
            "AI": "uid",
        }
    }
}';
        $tableMapper = json_decode(($json));
        $this->invoke_nonpublic_method($reader, 'validateJson', [$tableMapper, ['null', 'meta', 'tables']]);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_validateTableMapper_validJson_read_pass
     */
    public function test_validateTableMapper_validJson_read_pass ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader($this->tableName);

$json = '{
    "meta": {
        "generated": "2018-04-04 07:28:42",
        "database": "DB_NAME"
    },
    "tables": {
        "user_data": {
            "fields": [
                "id",
                "user_id",
                "cat_name",
                "dog_name"
            ],
            "null": [],
            "PK": "id",
            "AI": "id"
        },
        "users": {
            "fields": [
                "uid",
                "name",
                "email",
                "phone",
                "password",
                "address",
                "city",
                "created"
            ],
            "null": [],
            "PK": "uid",
            "AI": "uid"
        }
    }
}';

        $tableMapper = json_decode(($json));
        $this->assertInternalType('object', $tableMapper->tables->users);
        $this->assertEquals($tableMapper->tables->users, $reader->tableMapper);

        $this->assertInternalType('array', $reader->tableMapper->fields);
        $this->assertInternalType('array', $reader->tableMapper->null);
        $this->assertEmpty($reader->tableMapper->null);
        $this->assertInternalType('string', $reader->tableMapper->AI);
        $this->assertInternalType('string', $reader->tableMapper->PK);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_validateTableMapper_tableMapper_not_defined_fail
     *
     * @expectedException TypeError
     * expectedExceptionMessage Argument 1 passed to Tesis\DBLayer\System\SqlGeneratorReader::validateTableMapper() must be of the type array, none given
     */
    public function test_validateTableMapper_tableMapper_not_defined_fail ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader($this->tableName);

        $this->invoke_nonpublic_method($reader, 'validateTableMapper', []);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_validateTableMapper_tableMapper_empty_fail
     *
     * @expectedException TypeError
     * expectedExceptionMessage Argument 1 passed to Tesis\DBLayer\System\SqlGeneratorReader::validateTableMapper() must be of the type array, none given
     */
    public function test_validateTableMapper_tableMapper_empty_fail ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader($this->tableName);

        $tableMapper = '';

        $this->invoke_nonpublic_method($reader, 'validateTableMapper', [$tableMapper]);

        $this->assertTrue(true, 'Expected Pass');
    }


    /**
     * test_validateTableMapper_valid_tableMapper_pass
     */
    public function test_validateTableMapper_keyOmit_ini_pass ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader($this->tableName);
// Example of table mapper for ini
$ini = '
;File: mappers/dbTablesMapper.ini
;Database `test_db` tables generated: 04/10/2018 19:07:32

[user_data]
field[] = "id"
field[] = "user_id"
field[] = "cat_name"
field[] = "dog_name"
PK = "id"
AI = "id"

[users]
field[] = "uid"
field[] = "name"
field[] = "email"
field[] = "phone"
field[] = "password"
field[] = "address"
field[] = "city"
field[] = "created"
PK = "uid"
AI = "uid"';
        $tableMapper = parse_ini_string($ini, true);

        $this->invoke_nonpublic_method($reader, 'validateTableMapper', [$tableMapper]);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_read_inline_ini_valid_tableMapper_pass
     */
    public function test_validateTableMapper_valid_tableMapper_pass ()
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader($this->tableName);
// Example of table mapper for ini
$ini = '
;File: mappers/dbTablesMapper.ini
;Database `test_db` tables generated: 04/10/2018 19:07:32

[user_data]
field[] = "id"
field[] = "user_id"
field[] = "cat_name"
field[] = "dog_name"
PK = "id"
AI = "id"

[users]
field[] = "uid"
field[] = "name"
field[] = "email"
field[] = "phone"
field[] = "password"
field[] = "address"
field[] = "city"
field[] = "created"
PK = "uid"
AI = "uid"';
        $tableMapper = parse_ini_string($ini, true);

        $this->invoke_nonpublic_method($reader, 'validateTableMapper', [$tableMapper]);

        $this->assertTrue(true, 'Expected Pass');
    }

    /**
     * test_readFromJsonFinal
     * @dataProvider data_provider_file
     */
    public function test_read_mappers ($file, $function, $type, $pass, $expected)
    {
        $this->output(__METHOD__);

        $reader = new SqlGeneratorReader($this->tableName);

        $reader->tableMapper = [];

        $result = $this->data_provider_file();
        $this->invoke_nonpublic_method($reader, $function, [$file]);

        $this->assertInternalType($type, $reader->tableMapper, 'Pass: ' . $expected);
        if($file !== 'database/mappers/dbTablesMapper.json') {
            $this->assertEmpty($reader->tableMapper, 'Fail: ' . $expected);
            $this->assertFalse($pass, 'Expected Fail: ' . $expected);
        }
    }

    /**
     * data_provider_read_json_file
     */
    public function data_provider_file ()
    {
        return [
            ['database/mappers/dbTablesMapperNotValid.json' , 'readFromJSON' , 'array', false,  'Not valid json'],
            ['database/mappers/dbTablesMapperEmpty.json' , 'readFromJSON' , 'array', false,  'Empty json file'],
            ['database/mappers/dbTablesMapperNotValidProperty.json' , 'readFromJSON', 'array', false,  'Not valid property key'],
            ['database/mappers/dbTablesMapper.json' , 'readFromJSON' , 'object', true,  'Valid file'],

        ];
    }

/*---------------------- test real files --------------------------*/
    /**
     * test_readFromJson_pass
     * pass if file exists (for test rarelly)
     */
    public function test_readFromJson_pass()
    {
        $this->output(__METHOD__);

        $type = 'json'; // default
        if(file_exists($this->filename.'.'. $type)) {

            $reader = new SqlGeneratorReader($this->tableName, $type);

            $this->assertInternalType('object', $reader->tableMapper);
            $this->assertInternalType('array', $reader->tableMapper->fields);
            $this->assertInternalType('array', $reader->tableMapper->null);
            $this->assertEmpty($reader->tableMapper->null);
            $this->assertInternalType('string', $reader->tableMapper->AI);
            $this->assertInternalType('string', $reader->tableMapper->PK);
        } else {
            echo ' File does not exist ' . PHP_EOL;
            $this->assertTrue(true, 'pass: file not exists');
        }

    }

    public function test_readFromIni()
    {
        $this->output(__METHOD__);

        $type = 'ini';

        if(file_exists($this->filename.'.' . $type)) {

            $reader = new SqlGeneratorReader($this->tableName, $type);

            $this->assertInternalType('array', $reader->tableMapper);
            $this->assertInternalType('array', $reader->tableMapper['users']['field']);
            $this->assertInternalType('string', $reader->tableMapper['users']['AI']);
            $this->assertInternalType('string', $reader->tableMapper['users']['PK']);
        } else {
            echo ' File does not exist ' . PHP_EOL;
            $this->assertTrue(true, 'pass: file not exists');
        }

    }

    public function test_readFromArray()
    {
        $this->output(__METHOD__);

        $type = 'php';
        if(file_exists($this->filename.'.' . $type)) {
            $reader = new SqlGeneratorReader($this->tableName, $type);

            $this->assertInternalType('array', $reader->tableMapper);
            $this->assertInternalType('array', $reader->tableMapper['fields']);
            $this->assertInternalType('array', $reader->tableMapper['null']);
            $this->assertEmpty($reader->tableMapper['null']);
            $this->assertInternalType('string', $reader->tableMapper['AI']);
            $this->assertInternalType('string', $reader->tableMapper['PK']);
        } else {
            echo ' File does not exist ' . PHP_EOL;
            $this->assertTrue(true, 'pass: file not exists');
        }
    }

/*---------------- End of class ----------------------*/
}