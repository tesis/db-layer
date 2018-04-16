<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
/**
 * Summary: SqlGeneratorReader class provides mapping of all tables: fields,PK,AI
 */
/**
 * BaseModel class- class for databases
 *                   (dealing with select/insert/update/delete sql statements)
 *
 * PHP version 5.6
 *
 * @package    DBLayer
 * @subpackage Database_reader
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2016 Tereza Simcic <updated: 2018>
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       SqlGeneratorReader.php
 * @usage: SqlGeneratorReader should be called as needed:
 * - if tableMapping.php file does not exist
 * - on each change in DB schema like: inserting fields, changing
 * primary key or auto_increment
 * - it is added at the top of BaseModel - as DB reader
 */


namespace Tesis\DBLayer\System;

use Exception;
use InvalidArgumentException;
use Tesis\DBLayer\Exceptions\FileNotFoundException;
use Tesis\DBLayer\Exceptions\NotFoundException;
use Tesis\DBLayer\Exceptions\MapperNotValidException;
use Tesis\DBLayer\Exceptions\JsonNotValidException;

// require_once dirname(__DIR__) . '/config/bootstrap.php';

class SqlGeneratorReader
{
    /**
     * @access private
     * @var string
     */
    private $tableName;
    /**
     * @access public
     * @var string
     */
    public $filename = 'database/mappers/dbTablesMapper';
    /**
     * @access public
     * @var array
     */
    public $tableMapper = [];
    /**
     * @access public
     * @var array
     */
    public $results = [];
    /**
     * @access private
     * @var array
     */
    private $mapperKeys = ['fields', 'PK', 'AI'];


    /**
     * __construct options are json, ini, php, db
     *
     * @param object $conn
     *
     * @return
     */
    public function __construct($tableName = '', $type = 'json')
    {
        if (!isset($tableName) || empty($tableName)) {
            throw new InvalidArgumentException(' Empty table name');
        }

        $this->tableName = $tableName;
        $this->filename = $this->filename;

        if ($type === 'php') {
            $this->readFromArray();
        }
        if ($type === 'ini') {
            $this->readFromINI();
        }
        if($type === 'json') {
            $this->readFromJSON();
        }
    }

    private function validateFileExists($filename)
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException('File not found: ' . $filename);
        }
    }

    private function validateJson()
    {
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonNotValidException('Json not valid: ' . json_last_error_msg());
        }
    }

    /**
     * validate if table mapper is empty or not valid
     *          if table mapper keys are valid
     *          validation for ini or php
     *
     * @param  array      $tableMapper
     * @param  array|null $omitKeys
     *
     * @return throw Exception
     */
    private function validateTableMapper(array $tableMapper, array $omitKeys=null)
    {
        if (is_null($tableMapper)) {
            throw new InvalidArgumentException('Table mapper seems to be empty or not an array: ' . $tableMapper);
        }

        if(!is_null($omitKeys)) {
            foreach ($tableMapper as $key => $value) {
                if (!in_array($key, $omitKeys) && !in_array($key, $this->mapperKeys)) {

                    throw new MapperNotValidException('Required value in mapper is missing: ' . $key);
                }
            }
        }
    }

    /**
     * readFromArray
     *
     * @param  string $filename
     *
     * @return Exception|boolean
     */
    protected function readFromArray($filename='')
    {
        if(empty($filename)) {
            $filename = $this->filename . '.php';
        }

        try {
            $this->validateFileExists($filename);

            $content = include $filename;
            $tableMapper = $content[$this->tableName];
            try {
                $this->validateTableMapper($tableMapper, ['null']);

                $this->tableMapper = $tableMapper;
                return true;
            } catch (InvalidArgumentException $e) {
                echo 'Invalid arg: ' . $e->getMessage() . PHP_EOL;
            } catch (MapperNotValidException $e) {
                echo 'Not valid table: ' . $e->getMessage() . PHP_EOL;
            }
        } catch (FileNotFoundException $e) {
            echo 'FileNotFoundException: ' . $e->getMessage() . PHP_EOL;
        } catch (Exception $e) {
            echo 'Exception: ' . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * readFromJSON after successfull parsing, $tableMapper has to be an object
     *              with all tables and their properties set
     *
     * @param  string $filename
     *
     * @return Exception|boolean
     */
    protected function readFromJSON($filename='')
    {
        if(empty($filename)) {
            $filename = $this->filename . '.json';
        }

        try {
            $this->validateFileExists($filename);

            $content = file_get_contents($filename);
            $tableMapper = json_decode($content);

            try {
                $this->validateJson();

                if (!is_object($tableMapper->tables)) {
                    throw new InvalidArgumentException('Table mapper is not an object');
                }

                if(!property_exists($tableMapper->tables, $this->tableName)) {
                    throw new NotFoundException('This db table does not exist. Maybe you should regenerate new database mapper');
                }

                $this->tableMapper = $tableMapper->tables->{$this->tableName};
                return true;
            } catch (JsonNotValidException $e) {
                echo 'Json Exception: ' . $e->getMessage() . PHP_EOL;
            }
        } catch (FileNotFoundException $e) {
            echo 'FileNotFoundException: ' . $e->getMessage() . PHP_EOL;
        } catch (InvalidArgumentException $e) {
            echo 'Invalid arg: ' . $e->getMessage() . PHP_EOL;
        } catch (NotFoundException $e) {
            echo 'Not found table: ' . $e->getMessage() . PHP_EOL;
        } catch (Exception $e) {
            echo 'Exception: ' . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * readFromINI
     *
     * @param  string $filename
     *
     * @return Exception|boolean
     */
    protected function readFromINI($filename='')
    {
        if(empty($filename)) {
            $filename = $this->filename . '.ini';
        }

        try {
            $this->validateFileExists($filename);

            $tableMapper = parse_ini_file($filename, true);
            try {
                $this->validateTableMapper($tableMapper);

                $this->tableMapper = $tableMapper;
                return true;
            } catch (InvalidArgumentException $e) {
                echo 'Invalid arg: ' . $e->getMessage() . PHP_EOL;
            } catch (MapperNotValidException $e) {
                echo 'Not valid table: ' . $e->getMessage() . PHP_EOL;
            }
        } catch (FileNotFoundException $e) {
            echo 'FileNotFoundException: ' . $e->getMessage() . PHP_EOL;
        } catch (Exception $e) {
            echo 'Exception: ' . $e->getMessage() . PHP_EOL;
        }
    }

}
