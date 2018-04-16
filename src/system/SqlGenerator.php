<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
/**
 * Summary: SqlGenerator class provides mapping of all tables: fields,PK,AI
 */
/**
 * BaseModel class- class for databases
 *                   (dealing with select/insert/update/delete sql statements)
 *
 * PHP version 5.6
 *
 * @package    DBLayer
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2016 Tereza Simcic <updated: 2018>
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       sqlGenerator.php
 * @usage: sqlGenerator should be called as needed:
 * - if tableMapping.php file does not exist
 * - on each change in DB schema like: inserting fields, changing
 * primary key or auto_increment
 * - it is added at the top of BaseModel - as DB reader
 */
namespace Tesis\DBLayer\System;

use Exception;
use Tesis\DBLayer\Traits\ConnectDbTrait;
use Tesis\DBLayer\Traits\MySqliTrait;
use Tesis\DBLayer\Traits\DbManagerTrait;

// require_once dirname(__DIR__) . '/config/bootstrap.php';

class SqlGenerator
{
    use ConnectDbTrait, MySqliTrait, DbManagerTrait;

    /**
     * @access protected
     * @var object
     */
    public $conn;
    /**
     * @access protected
     * @var string
     */
    public $database;
    /**
     * @access protected
     * @var array
     */
    public $tables = [];
    /**
     * @access public
     * @var string
     */
    public $typeOptions = ['php', 'ini', 'json'];
    /**
     * @access public
     * @var string
     */
    public $content;
    /**
     * @access public
     * @var string
     */
    public $type='json';
    /**
     * @access public
     * @var string
     */
    public $mapperDir = 'mappers';
    /**
     * @var string
     */
    public $mapperFile = 'dbTablesMapper';
    /**
     * @var string
     */
    public $mapperType = 'json';
    /**
     * @access public
     * @var boolean
     */
    public $compact = false;
    /**
     * @access public
     * error mesages we might want to return
     * @var string
     */
    public $error = '';
    /**
     * @access public
     * @var string
     */
    public $sql = '';

    /**
     * __construct
     *
     * @param object $conn
     *
     * @return
     */
    public function __construct($conn = null)
    {
        $this->openConnection($conn);
    }

    /**
     * checkTables
     */
    public function checkTables ()
    {
        $sql = "SHOW TABLES FROM " . $this->database . ";";
        $res = $this->conn->query($sql);

        if (!$res) {
            $this->error = 'Database error: ' . $this->conn;
            return false;
        }
        if($res->num_rows === 0) {
            $this->error = 'Database error: no tables';
            return false;
        }
        return $res;
    }
    /**
     * getAllTables get all tables from database and assign them to class
     *              variables tables
     *              get content
     *              generate mapper file
     *
     * @param string $type type of file to generate (ini, php, json = default)
     *
     * @return void
     */
    public function getAllTables($type = '')
    {
        $this->mapperType = $type;
        if (empty($type) || !in_array($type, $this->typeOptions)) {
            $this->mapperType = 'json';
        }

        $res = $this->checkTables();
        if(!$res) {
            return false;
        }

        while ($row = $res->fetch_row()) {
            $row = reset($row);
            $this->tables[] = $row;
        }
        try {
            $this->getContent();

            $this->generateFile();
        } catch (Exception $e) {
            assignExceptions($this, $e, false);
        }
    }

    /**
     * getContent get content as an array or string (if type == json)
     *            assign to the class var $content
     *            or throw an error
     */
    protected function getContent()
    {
        $content = [];
        $ct = [];
        if (empty($this->tables)) {
            throw new Exception('Empty tables');
        }
        foreach ($this->tables as $tableName) {
            $ct[] = $tableName;
            try {
                $content[$tableName] = $this->getAllFields($tableName);
            } catch (Exception $e) {
                $content[$tableName] = [];
                assignExceptions($this, $e, false);
            }
        }
        if (empty($content)) {
            throw new Exception('Empty tables - check tables');
        }

        if ($this->mapperType === 'ini') {
            $this->content = join('', $content);
        } else {
            $this->content = $content;
        }
    }
    /**
     * getAllFields
     *
     * @param  string $tableName
     *
     * @return array|bool
     */
    protected function getAllFields($tableName = '')
    {
        if (empty($tableName)) {
            throw new InvalidArgumentException('Empty table');
        }

        $sql = "DESCRIBE " . $tableName;
        $result = $this->conn->query($sql);

        if (!$result) {
            throw new Exception('Empty result');
        }
        $rows = [];
        while ($row = $result->fetch_array()) {
            $rows[] = $row;
        }

        if (!is_array($rows) || empty($rows)) {
            throw new Exception('NO rows');
        }

        return $this->parseRows($tableName, $rows);
    }

    /**
     * parseRows
     *    tableName, rows params are already checked, no need to check them again
     *
     * @param  string     $tableName
     * @param  array|null $columns properties: Field (fieldName),
     *                    Type(int, varchar, ...), Null(yes/no),
     *                    PRI (primary key name), Default (default value if any)
     *                    and Extra(auto_increment)
     *
     * @return array
     */
    protected function parseRows($tableName, $columns)
    {
        $colKeys = ['Field', 'Type', 'Null', 'Default', 'Extra'];
        $tablePK = "";
        $tableAI = "";
        $tableFields = [];
        $nullFields = [];

        foreach ($columns as $key => $column) {
            $name = $column['Field'];

            if (!empty($column['Extra']) && $column['Extra'] === 'auto_increment') {
                $tableAI = $name;
            }
            if (!empty($column['Key']) && $column['Key'] === 'PRI') {
                $tablePK = $name;
            }
            if ($column['Null'] == 'YES') {
                $nullFields[] = $name;
            }
            $tableFields[] = $name;
        }

// INI
        if ($this->mapperType === 'ini') {
            $ini = '[' . $tableName . ']' . PHP_EOL;
            foreach ($tableFields as $field) {
                $ini .= 'field[] = "' . $field . '"' . PHP_EOL;
            }

            foreach ($nullFields as $null) {
                $ini .= 'null[] = "' . $null . '"' . PHP_EOL;
            }
            $ini .= 'PK = "' . $tablePK . '"' . PHP_EOL;
            $ini .= 'AI = "' . $tableAI . '"' . PHP_EOL . PHP_EOL;

            return $ini;
        }
// JSON || PHP
        else {
            return [
                'fields' => $tableFields,
                'null' => $nullFields,
                'PK' => $tablePK,
                'AI' => $tableAI,
            ];
        }
    }

    /**
     * generatePHPFile
     *
     * @return void
     */
    public function generateFile()
    {
        switch ($this->mapperType) {
            case 'json':
                // $this->mapperType = 'json';
                $content = $this->generateJSONFile();
                break;

            case 'ini':
                $this->mapperType = 'ini';
                $content = $this->generateINIFile();
                break;

            default:
                $this->mapperType = 'php';
                $content = $this->generatePHPFile();
                break;
        }
        $this->filename = 'database/' . $this->mapperDir . '/' . $this->mapperFile . '.' . $this->mapperType;
        file_put_contents($this->filename, $content);
    }

    /**
     * generatePHPFile
     *
     * @return string
     */
    public function generatePHPFile()
    {
        if (empty($this->content)) {
            throw new Exception('Content is empty');
        }

        $this->filename = $this->mapperDir . '/' . $this->mapperFile . '.' . $this->mapperType;
        // Write the content to the file
        $fileContent = "<?php ";
        $fileContent .= "// File: " . $this->filename ;
        $fileContent .= PHP_EOL . "// Database `" . $this->database . "` tables generated: " . $this->date . PHP_EOL . PHP_EOL;
        // Don't miss 'return ' ->otherwise arrays will not be searched,
        // error in loader
        $f = json_encode($this->content, JSON_PRETTY_PRINT);
        $f = str_replace('{', '[', $f);
        $f = str_replace('}', ']', $f);
        $f = str_replace(':', '=>', $f);
        $fileContent .= "return $" . "tables = ";
        $fileContent .= $f;
        $fileContent .= ";";

        return $fileContent;
    }

    /**
     * generateJSONFile
     *
     * @return string
     */
    public function generateJSONFile()
    {
        if (empty($this->content)) {
            throw new Exception('Content is empty');
        }
        // Write the content to the file

        $write = [
            "meta" => [
                'generated' => date("Y-m-d H:i:s"),
                'database' => $this->database,
            ],
            "tables" => $this->content,
        ];
        $fileContent = json_encode($write, JSON_PRETTY_PRINT);
        // $fileContent .= EOL . '}';

        return $fileContent;
    }

    /**
     * generateINIFile
     *
     * @return string
     */
    public function generateINIFile()
    {
        if (empty($this->content)) {
            throw new Exception('Content is empty');
        }
        $this->filename = $this->mapperDir . '/' . $this->mapperFile . '.' . $this->mapperType;
        // Write the content to the file
        $fileContent = ";File: " . $this->filename . PHP_EOL;
        $fileContent .= ";Database `" . $this->database . "` tables generated: " . $this->date . PHP_EOL . PHP_EOL;
        $fileContent .= $this->content;

        return $fileContent;
    }

    /**
     * readTables
     *
     * @return array
     */
    public function readTables()
    {
        return $this->tables;
    }
}
