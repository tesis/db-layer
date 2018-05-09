<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
/**
 * Summary: Abstract class for mysqli and pdo layer
 */
/**
 * DBBase is the abstract class for managing mysqli and pdo layer
 *        containing common methods
 *
 * PHP version 5.6 / 7.0
 *
 * @package    DBLayer
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2018 Tereza Simcic
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       DBLayer.php
 */

namespace Tesis\DBLayer\Database;

use Exception;
use InvalidArgumentException;
use RuntimeException;

use Tesis\DBLayer\System\SqlGeneratorReader;
use Tesis\DBLayer\Traits\DbManagerTrait;
use Tesis\DBLayer\Traits\SelectTrait;
use Tesis\DBLayer\Traits\ConnectDbTrait;

abstract class DBBase
{
    use SelectTrait, DbManagerTrait, ConnectDbTrait;

    /**
     * $db object
     * @access public
     * @var obj
     */
    public $conn = null;
    /**
     * dbName
     * @access public
     * @var string
     */
    public $database;
    /**
     * @access public
     * error mesages we might want to return
     * @var string
     */
    public $error = '';
    /**
     * @access public
     * error mesages we might want to return
     * @var array
     */
    public $errorInfo = [];
    /**
     * @access public
     * @var string
     */
    public $type = 'json';
    /**
     * @access public
     * @var boolean
     */
    public $validate = true;
    /**
     * @access public
     * @var string
     */
    public $sql = '';
    /**
     * @access protected
     * @var array
     */
    public $fields;
    /**
     * @access protected
     * @var array
     */
    public $values;
    /**
     * @access public
     * @var string
     */
    public $tableName;
    /**
     * @access public
     * @var array
     */
    public $tableFields = [];
    /**
     * @access public
     * @var string
     */
    public $tablePK;
    /**
     * @access public
     * @var string
     */
    public $tableAI;
    /**
     * @access public
     * @var array
     */
    public $tableNull = [];
    /**
     * @access public
     * @var array
     */
    public $data = [];
    /**
     * @access public
     * @var string
     */
    public $action = '';
    /**
     * @access private
     * @var string
     */
    private $id = '';
    /**
     * @access public
     * @var integer
     */
    public $affectedRows = null;
//------ build chained queries
    /**
     * @access private
     * @var string
     *
     */
    public $select = '*';
    /**
     * where statement from child class
     * @access private
     * @var string
     *
     */
    public $where = '';
    /**
     * @access private
     * @var integer
     *
     */
    public $limit;
    /**
     * @access private
     * @var string
     *
     */
    public $orderBy = '';
    /**
     * @access private
     * @var string
     *
     */
    public $groupBy = '';
    /**
     * @access private
     * @var string
     */
    public $join = '';

    /**
     * @access protected
     * @var object
     */
    public $reader;

    /**
     * __construct
     *
     * @param object|array|null $conn
     */
    public function __construct($conn=null)
    {
        $this->openConnection($conn);
    }

    /**
     * readFromDb
     *            assign results to class variable
     *            dataTypes included in ['field']['dataType']
     *            separatelly assign class variables fields, AI, PK
     *            method handy to check properties of any table in DB
     *
     * @param string $tableName
     *
     * @usage: reading exact properties for a table to make custom insert
     *         where not all fields are defined
     *         reading propertis will overcome issues with
     *         'default value not assigned'
     *
     * @return array
     */
    public function readFromDb ($tableName='')
    {
        if(!empty($tableName)) {
            $this->tableName = $tableName;
        }

        if(empty($this->tableName)) {
            throw new InvalidArgumentException('Table name empty');
        }

        $sql = 'DESCRIBE ' . $this->tableName;
        $this->results = [];

        $res = $this->executeSql($sql);
        if($res) {
            $i = 0;
            foreach ($res as $key => $row) {
                $row = (array)$row;
                list($dataType) = explode("(", $row['Type']);
                $row['dataType'] = $dataType;
                $this->results[] = $row;
                $this->tableMapper['dataType'][$row['Field']] = $dataType;
                $this->tableMapper['fields'][$i] = $row['Field'];
                if ($row['Null'] == 'YES') {
                    $this->tableMapper['null'][$i] = $row['Field'];
                }
                if($row['Key'] !== '' && $row['Key'] === 'PRI') {
                    $this->tableMapper['PK'] = $row['Field'];
                }
                if($row['Extra'] !== '' && $row['Extra'] === 'auto_increment') {
                    $this->tableMapper['AI'] = $row['Field'];
                }
                $i++;
            }
        }
    }

    /**
     * table define table to read
     *       set validation
     *       if true, read properties from mapper, otherwise skip
     *
     * @param  string  $tableName
     * @param  boolean $validate by default set to true
     *                           (recommended to prevent SQL injections)
     *
     * @return object
     */
    public function table ($tableName = '', $validate = true)
    {
        if(empty($tableName)) {
            throw new InvalidArgumentException('Table name empty ');
        }
        $this->tableName = $tableName;
        $this->validate = $validate;
        if($this->validate) {
            $reader = new SqlGeneratorReader($tableName);
            if(!$reader) {
                throw new Exception("Cannot read mapper");
            }
            $this->tableFields = $reader->tableMapper->fields;
            $this->tablePK = $reader->tableMapper->PK;
            $this->tableAI = $reader->tableMapper->AI;
            $this->tableNull = $reader->tableMapper->null;
        }

        return $this;
    }

    /**
     * prepareInsertFieldsValues
     *
     * @param  array|null $data
     * @param  boolean    $custom for custom insert, where rading DB table
     *                            property instead of mapper property
     *
     * @return void
     */
    protected function prepareInsertFieldsValues(array $data = null, $custom = false)
    {
        if (is_null($data)) {
            $this->error = 'Empty data';
            throw new InvalidArgumentException($this->error);
        }

        if(empty($this->tableFields)) {
            $this->error = 'Table fields are empty';
            throw new RuntimeException($this->error);
        }

        $this->getValuesToInsert($data, $custom);


        if (empty($this->fields) || empty($this->values)) {
            $this->error = 'Fields/Values are empty';
            throw new RuntimeException($this->error);
        }
    }

    /**
     * getValuesToInsert
     *
     * @param  array   $data
     * @param  boolean $custom
     *
     * @return void
     */
    protected function getValuesToInsert ($data, $custom = false)
    {
        if($custom === false) {
            foreach ($data as $key => $val) {
                if (in_array($key, $this->tableFields)) {
                    $this->fields[] = $key;
                    $this->values[] = trim($val);
                }
            }
        }
        else {
            $this->getValuesToInsertCustom($data);
        }
    }

    /**
     * getValuesToInsertCustom if reading fields from database,
     *                         not the mapper
     *
     * @param array $data pass array of data
     */
    protected function getValuesToInsertCustom ($data)
    {
        foreach($this->tableFields as $k => $c) {

            if($c === $this->tablePK) {
                continue;
            }
            $this->fields[] = $c;
            if(array_key_exists($c, $data)) {
                $this->values[] = trim($data[$c]);
            }
            else {
                $val = "";
                if($this->results[$k]['dataType'] === 'int' || $this->results[$k]['dataType'] === 'float' || $this->results[$k]['dataType'] === 'double') {
                    $val = 0;
                }
                if($this->results[$k]['dataType'] === 'date' || $this->results[$k]['dataType'] === 'datetime' || $this->results[$k]['dataType'] === 'timestamp') {
                    // set current date or set mode to ''
                    $val = '0000-00-00';
                }
                $this->values[] = $val;
            }
        }
    }
    /**
     * validateInsertFields check if all not null fields/values in a table
     *                     are provided
     *
     * @return boolean|void
     */
    protected function validateInsertFields()
    {
        if (!is_array($this->fields)) {
            throw new RuntimeException('Fields are not an array: ' . print_r($this->fields, true));
        }
        
        if(!empty($this->tableAI)) {
            // PK and AI are always at position 0
            // $k = array_search($this->tableAI, $this->tableFields);
            unset($this->tableFields[0]);
        }

        $missedFields = array_diff($this->tableFields, $this->fields);
        if(!empty($this->tableNull)) {
            $missedFields = array_diff($missedFields, $this->tableNull);
        }

        if (!empty($missedFields)) {
            $error = 'Missing required fields: ' . join(', ', $missedFields);
            throw new RuntimeException($error);
        }

        return true;
    }

    /**
     * validateWhere
     *
     * @param  string $field
     * @param  string $value
     * @param  string $operator
     * @param  string $type
     *
     * @return string
     */
    protected function validateWhere($field = '', $value = '', $operator = '=', $type = "WHERE")
    {
        if (empty($field)) {
            throw new InvalidArgumentException('Field is empty');
        }

        if (!in_array($field, $this->tableFields)) {
            $this->error = ' Field ' . $field . ' does not exist in a table';
        }
        // For PDO layer
        if($this->dbDriver === 'PDO') {
            $this->data[$field] = $value;
        }

        $this->values[] = $value;

        if ($this->where === '') {
            $this->where = ' WHERE ' . $this->wherePreparedStatement($field, $operator);
            return;
        }

        // OR / AND
        $this->where .= ' ' . $type . ' ' . $this->wherePreparedStatement($field, $operator);
    }

    /**
     * reset reset class variables
     */
    protected function reset ()
    {
        $this->error = '';
        $this->errorInfo = [];
        $this->values = [];
        $this->fields = [];
        $this->data = [];
    }

    /**
     * get concatenate select where, groupBy, orderBy, limit and create
     *     prepared statement for exec
     *
     * @param  string $limit
     *
     * @return void
     */
    public function get($limit = null)
    {
        $this->action = __FUNCTION__;

        if (!is_null($limit)) {
            $limit = ' LIMIT ' . $limit;
        }

        $sql = 'SELECT ' . $this->select
        . ' FROM ' . $this->tableName
        . $this->join
        . $this->where
        // . $this->groupBy
        . $this->orderBy
            . $limit;

        $this->sql = $sql;

        return $sql;
    }

    /**
     * get first record
     */
    public function first()
    {
        $this->limit = 1;
        $this->sql = $this->get() . ' LIMIT ' . $this->limit;
    }

    /**
     * get all records, no limit
     */
    public function all()
    {
        $this->sql = $this->get();
    }

    /*---------------- End of class ----------------------*/
}
