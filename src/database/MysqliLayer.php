<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
/**
 * Summary: MySqli layer provides custom methods based on mysqli extension
 */
/**
 * MySqliLayer class based on mysqli extension
 *
 * PHP version 5.6 / 7.0
 *
 * @package    DBLayer
 * @subpackage Mysqli_layer
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2018 Tereza Simcic
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       MysqliLayer.php
 * @usage:
 */

namespace Tesis\DBLayer\Database;

use Exception;
use InvalidArgumentException;
use RuntimeException;

use mysqli;
use Tesis\DBLayer\Database\DBBase;
use Tesis\DBLayer\Faces\DbAdapterInterface;
use Tesis\DBLayer\Traits\ConnectDbTrait;
use Tesis\DBLayer\Traits\MySqliTrait;


mysqli_report(MYSQLI_REPORT_STRICT);

class MySqliLayer extends DBBase implements DbAdapterInterface
{
    use ConnectDbTrait, MySqliTrait;

    /**
     * @access protected
     * @var string
     */
    protected $placeholder = '?';
    /**
     * @access protected
     * @var string
     */
    protected $dbDriver = 'mysqli';

    /**
     * insert create prepared statement and insert into database
     *        1- prepare query
     *        2- bind params
     *        3- execute
     *        escaping string not needed, records should be safe
     *
     * @param  array   $data
     *
     * @return void
     */
    public function insert(array $data = null)
    {
        $this->action = __FUNCTION__;

        $this->reset();

        try {
            $this->prepareInsertFieldsValues($data);

            // Default is true, can be set to false when selecting table
            // @see DBBase::table()
            if($this->validate) {
                $this->validateInsertFields();
            }

            $vals = array_fill(0, count($this->values), $this->placeholder);
            $sql = "INSERT INTO " . $this->tableName . " (" . join(',', $this->fields) . ") VALUES (" . join(',', $vals) . ")";

            $this->sql = $sql;

            $this->process();
        } catch (Exception $e) {
            assignExceptions($this, $e);
        }

    }
    /**
     * insertCustom insert only some values, other not known and
     *              if column is set to NOT NULL this can cause an error,
     *              thus insert dummy data for the rest of the fields
     *
     *              column declaration should be known, otherwise another
     *              error would be thrown - 'Incorrect value .. for column'
     *
     *              solution: read table declaration from the database
     *              and prvide data for declared field types
     *
     * @param  string     $tableName
     * @param  array|null $data
     *
     * @usage: only for special cases, not recommended
     *
     * @return boolean
     */
    public function insertCustom(array $data = null)
    {
        $this->action = __FUNCTION__;

        $this->reset();
        try {
            $this->conn->query("SET sql_mode = ''");

            $this->readFromDb();

            $this->tableFields = $this->tableMapper['fields'];
            $this->tablePK = $this->tableMapper['PK'];
            $this->tableAI = $this->tableMapper['AI'];
            $this->tableNull = isset($this->tableMapper['null']) ? $this->tableMapper['null'] : null;

            $this->prepareInsertFieldsValues($data, true);

            $vals = array_fill(0, count($this->values), $this->placeholder);
            $sql = "INSERT INTO " . $this->tableName . " (" . join(',', $this->fields) . ") VALUES (" . join(',', $vals) . ")";

            $this->sql = $sql;

            $this->process();
        } catch (Exception $e) {
            assignExceptions($this, $e);
        }
    }

    /**
     * update record
     *
     * @param  array|null $data $data contain id value too
     *
     * @return void
     */
    public function update(array $data = null)
    {
        $this->action = __FUNCTION__;

        $this->reset();

        try {
            $this->prepareUpdateFieldsValues($data);

            // if (!array_key_exists($this->tablePK, $data)) {
            //     $this->error = 'Data does not have PK set: ' . $this->tablePK;
            //     throw new RuntimeException($this->error);
            // }

            $sql = 'UPDATE ' . $this->tableName . ' SET ' . join(',', $this->fields);
            $sql .= ' WHERE ' . $this->tablePK . ' = ' . $this->placeholder;

            $this->sql = $sql;

            $this->process();

        } catch (Exception $e) {
            assignExceptions($this, $e);
        }
    }

    /**
     * prepareUpdateFieldsValues append at the end of values id to repplace
     *                           placeholder in where, ie: where id=?
     *
     * @param  array|null $data
     * @param  boolean    $validateNull check if table field is allowed to be
     *                                  null or not
     *
     * @return void
     */
    protected function prepareUpdateFieldsValues(array $data = null)
    {
        if (is_null($data)) {
            $this->error = 'Empty data';
            throw new InvalidArgumentException($this->error);
        }

        if(empty($this->tableFields) || empty($this->tablePK)) {
            $this->error = 'Table fields / PK are empty';
            throw new RuntimeException($this->error);
        }

        foreach ($data as $key => $val) {
            // Skip id field for autoincremented table - only
            if (in_array($key, $this->tableFields) && $key != $this->tablePK) {
                $this->fields[] = $key . '=' . $this->placeholder;
                $this->values[] = trim($val);
            }
        }

        $this->id = isset($data[$this->tablePK]) ? $data[$this->tablePK] : '';
        $this->values[] = $this->id;

        if (empty($this->id) || empty($this->values)) {
            $this->error = 'Id/Values are empty';
            throw new RuntimeException($this->error);
        }
    }

    /**
     * delete
     *
     * @param  integer $id
     * @param  string  $fieldId unique field or primary key
     *
     * @return void
     */
    public function delete($id = null, $fieldId = '')
    {
        $this->action = __FUNCTION__;

        $this->reset();

        try {
            if (is_null($id)) {
                $this->error = 'ID is required';
                throw new InvalidArgumentException($this->error);
            }

            if (empty($fieldId)) {
                $fieldId = $this->tablePK;
            }

            $sql = 'DELETE FROM ' . $this->tableName . ' WHERE ' . $fieldId . ' = ' . $this->placeholder;

            $this->sql = $sql;

            $this->id = $id;
            $this->values[] = $this->id;

            $this->execute();
        } catch (Exception $e) {
            assignExceptions($this, $e);
        }
    }

    /**
     * process
     */
    protected function process ()
    {
        if(is_null($this->conn)) {
            throw new Exception('no connection');
        }
        if (empty($this->sql)) {
            $this->error = 'Empty SQL';
            throw new Exception($this->error);
        }

        if (is_null($this->tableName) || empty($this->tableName)) {
            $this->error = 'Empty tableName';
            throw new Exception($this->error);
        }
        // Uncaught Error: Call to a member function bind_param() on boolean
        if(!$this->stmt = $this->conn->prepare($this->sql)) {
            throw new Exception('conn prepare error: ' . $this->conn->error);
        }

        // Exclude for select statements
        if (!empty($this->values)) {
            $bind = array_fill(0, count($this->values), 's');
            $this->stmt->bind_param(join('', $bind), ...$this->values);
        }

        $exec = $this->stmt->execute();

        if (!$exec && $this->stmt->error !== '') {
            // TODO: debug
            // $this->debug(__METHOD__);

            $this->affectedRows = $this->conn->affected_rows;

            throw new Exception($this->stmt->error);
        }
        // Only for CRUD operations
        $this->affectedRows = $this->conn->affected_rows;

        // Fetch results
        if ($this->action !== 'get') {
            $this->reset();
            $this->stmt->close();
        } else {
            $res = $this->fetchResults();
            $this->reset();
            return $res;
        }
    }
    /**
     * execute execute prepared statement and return results for select
     *         statements
     *         for select stataments assigns array of objects to class values
     *
     * @return void
     */
    public function execute ()
    {
        try {
            return $this->process();
        } catch (InvalidArgumentException $e) {
            assignExceptions($this, $e);
        } catch (Exception $e) {
            assignExceptions($this, $e);
        }
    }

    /**
     * fetch fetch result(s)
     *
     * @return void
     */
    protected function fetchResults()
    {
        $this->action = __FUNCTION__;
        $this->reset();
        if (is_null($this->stmt)) {
            $this->error = 'Smtp is null';
            throw new Exception($this->error);
        }

        $res = explode(',', $this->select);

        // bind result variables
        $this->stmt->bind_result(...$res);

        // Fetch multiple rows
        $result = $this->stmt->get_result();
        $results = $this->fetchRows($result);
        $this->affectedRows = $this->conn->affected_rows;
        // $this->values = $results;
        $this->stmt->close();
        return $results;
    }

    /**
     * executeSql execute query without prepared statements
     *
     * @param string $sql
     *
     * return array
     */
    public function executeSql($sql = '')
    {
        if (empty($sql)) {
            $this->error = 'Empty sql';
            throw new InvalidArgumentException($this->error);
        }

        $result = $this->conn->query($sql);
        $results = $this->fetchRows($result);
        $this->sql = '';
        // Free result set
        $result->close();

        return $results;
    }

    /**
     * fetchRows
     *
     * @param  object $result
     *
     * @return array
     */
    protected function fetchRows($result)
    {
        if (!$result) {
            return false;
        }
        $results = [];
        // Cycle through results
        while ($row = $result->fetch_object()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * wherePreparedStatement append where string with placeholder
     *
     * @param  string $field
     * @param  string $operator =, > , > ---
     *
     * @return string
     */
    protected function wherePreparedStatement($field, $operator)
    {
        return $field . $operator . ' ? ';
    }

    /*---------------- End of class ----------------------*/
}
