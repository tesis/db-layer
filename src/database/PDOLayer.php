<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
/**
 * Summary: PDO layer provides custom methods based on PDO class
 */
/**
 * PDO layer class for PDO library
 *
 * PHP version 5.6 / 7.0
 *
 * @package    DBLayer
 * @subpackage PDOlayer
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2018 Tereza Simcic
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       PDOLayer.php
 * @usage:
 */

namespace Tesis\DBLayer\Database;

use Exception;
use InvalidArgumentException;
use RuntimeException;

use PDO;
use Tesis\DBLayer\Database\DBBase;
use Tesis\DBLayer\Faces\DbAdapterInterface;
use Tesis\DBLayer\Traits\ConnectDbTrait;
use Tesis\DBLayer\Traits\PDOTrait;

class PDOLayer extends DBBase implements DbAdapterInterface
{
    use ConnectDbTrait, PDOTrait;

    /**
     * @access protected
     * @var string
     */
    protected $dbDriver = 'PDO';
    /**
     * @access protected
     * @var string
     */
    protected $dbCharset = 'utf8';

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
            $bind = [];
            $bindFields = [];

            $this->prepareInsertFieldsValues($data);

            if($this->validate) {
                $this->validateInsertFields();
            }

            foreach ($this->fields as $field) {
                $bind[] = ':' . $field;
                $bindFields[] = "`" . str_replace("`", "``", $field) . "`";
            }

            $sql = "INSERT INTO " . $this->tableName . " (" . implode(',', $bindFields) . ") VALUES (" . implode(',', $bind) . ")";

            $this->sql = $sql;

            $this->conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

            $dbh = $this->conn->prepare($sql);
            foreach ($this->fields as $key => $value) {
                $dbh->bindValue(':' . $value, $this->values[$key], \PDO::PARAM_STR);
            }
            $stm = $dbh->execute();
            $this->affectedRows = $dbh->rowCount();
        } catch (Exception $e) {
            $this->affectedRows = 0;
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

            $bind = [];
            $bindFields = [];

            foreach ($this->fields as $field) {
                $bind[] = ':' . $field;
                $bindFields[] = "`" . str_replace("`", "``", $field) . "`";
            }

            $sql = "INSERT INTO " . $this->tableName . " (" . implode(',', $bindFields) . ") VALUES (" . implode(',', $bind) . ")";

            $this->sql = $sql;

            $this->conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

            $dbh = $this->conn->prepare($sql);
            foreach ($this->fields as $key => $value) {
                $dbh->bindValue(':' . $value, $this->values[$key], \PDO::PARAM_STR);
            }
            $dbh->execute();
            $this->affectedRows = $dbh->rowCount();

        } catch (Exception $e) {
            $this->affectedRows = 0;
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

            if (!array_key_exists($this->tablePK, $this->data)) {
                $this->error = 'Data does not have PK set: ' . $this->tablePK;
                throw new Exception($this->error);
            }
            $sql = 'UPDATE ' . $this->tableName . ' SET ' . join(',', $this->fields);
            $sql .= ' WHERE ' . $this->tablePK . ' = :' . $this->tablePK;

            $this->sql = $sql;
            $dbh = $this->conn;
            $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

            // test if this->>data has uid!!!
            $dbh = $this->conn->prepare($sql);
            $dbh->execute($this->data);
            $this->affectedRows = $dbh->rowCount();

        } catch (Exception $e) {
            $this->affectedRows = 0;
            assignExceptions($this, $e);
        }
    }

    /**
     * prepareUpdateFieldsValues append at the end of values id to repplace
     *                           placeholder in where, ie: where id=?
     *
     * @param  array|null $data
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
            // allow only fields that exists in db schema
            if (in_array($key, $this->tableFields) && $key != $this->tablePK) {
                // Extra protection with backticks
                $this->fields[] = "`" . str_replace("`", "``", $key) . "` = :" . $key;
                $this->data[$key] = trim($val);
            }
        }

        $this->id = isset($data[$this->tablePK]) ? $data[$this->tablePK] : '';
        $this->data[$this->tablePK] = $this->id;

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
     * @return
     */
    public function delete($id = null, $fieldId = '')
    {
        $this->action = __FUNCTION__;

        $this->reset();

        try{
            if (is_null($id)) {
                $this->error = 'ID is required';
                throw new InvalidArgumentException($this->error);
            }

            if (empty($fieldId)) {
                $fieldId = $this->tablePK;
            }

            $sql = 'DELETE FROM ' . $this->tableName . ' WHERE ' . $fieldId . ' = :' . $fieldId;

            $this->sql = $sql;

            $this->id = $id;
            $this->data[$fieldId] = $this->id;

            $this->conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

            $dbh = $this->conn->prepare($sql);
            $dbh->execute($this->data);
            $this->affectedRows = $dbh->rowCount();
        } catch (Exception $e) {
            $this->affectedRows = 0;
            assignExceptions($this, $e);
        }
    }

    /**
     * process - only for select statemenst for PDO
     */
    public function process ()
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

        $this->conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

        $this->stmt = $this->conn->prepare($this->sql);

        // Uncaught Error: Call to a member function bind_param() on boolean
        // Check if this causes an issue
        if(!$this->stmt) {
            throw new Exception('conn prepare error: ' . $this->conn->error);
        }

        // data array created in validateWhere()
        foreach ($this->data as $key => $value) {
            $this->stmt->bindValue(':' . $key, $this->data[$key]);
        }

        $exec = $this->stmt->execute();

        if (!$exec && $this->stmt->error !== '') {
            // TODO: debug
            // $this->debug(__METHOD__);

            $this->affectedRows = $this->stmt->rowCount();

            throw new Exception($this->stmt->error);
        }

        // Only for CRUD operations
        $this->affectedRows = $this->stmt->rowCount();

        // Fetch results
        if ($this->action !== 'get') {
            $this->reset();
        } else {
            $res = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
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
     * executeSql execute query without prepared statements
     *
     * @param string $sql
     *
     * return array
     */
    public function executeSql($sql = '')
    {
        if (empty($sql)) {
            $this->error = 'Empty SQL';
            throw new Exception($this->error);
        }

        $stmt = $this->conn->query($sql);

        if (!$stmt) {
            $this->error = "Not executed query";
            throw new Exception($this->error);
        }
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $res;
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
        return $field . ' ' . $operator . ' :' . $field;
    }


    /*---------------- End of class ----------------------*/
}
