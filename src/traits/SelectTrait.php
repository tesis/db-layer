<?php
//File: src/traits/SelectTrait.php
/**
 * Summary: trait for database select statements, in use with mysqli extension
 *                    and PDO class
 */
/**
 * SelectTrait
 *
 * PHP version 5.6 / 7.0
 *
 * @package    DBLayer
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2018 Tereza Simcic <updated: 2018>
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       SelectTrait.php
 *
 */

namespace Tesis\DBLayer\Traits;

// use mysqli;
use Exception;

trait SelectTrait
{
    /**
     * select
     *
     * @param  string  $fields
     *
     * @return object
     */
    public function select($fields = null)
    {
        if(empty($this->tableName)) {
            $this->error = 'Empy table name';
            throw new Exception($this->error);
        }
        if (is_null($fields) || empty($fields)) {
            $fields = '*';
        }

        $this->resetSelect();

        $this->validateSelectFields($fields);

        return $this;
    }

    /**
     * resetSelect before start building new select statement
     *             reset all class variables for select
     */
    protected function resetSelect()
    {
        $this->select = '';
        $this->where = '';
        $this->join = '';
        $this->groupBy = '';
        $this->orderBy = '';
        $this->orWhere = '';
        $this->andWhere = '';
        $this->limit = '';
    }

    /**
     * join
     *
     * @param  string $table
     * @param  string $on
     * @param  string $type
     *
     * @return object
     */
    public function join($table, $on, $type = '')
    {
        $this->join = $type . ' JOIN ' . $table . ' ON ' . $on;
        return $this;
    }

    /**
     * where build where statement with whereAnd and whereOr
     *
     * @param  string $field
     * @param  string $value
     * @param  string $operator
     *
     * @return object
     */
    public function where($field = '', $value = '', $operator = '=')
    {
        $this->validateWhere($field, $value, $operator);
        return $this;
    }

    /**
     * orWhere build where statement with whereAnd and whereOr
     *
     * @param  string $field
     * @param  string $value
     * @param  string $operator
     *
     * @return object
     */
    public function orWhere($field = '', $value = '', $operator = '=')
    {
        $this->validateWhere($field, $value, $operator, $type = "OR");
        return $this;
    }

    /**
     * andWhere build where statement with whereAnd and whereOr
     *
     * @param  string $field
     * @param  string $value
     * @param  string $operator
     *
     * @return object
     */
    public function andWhere($field = '', $value = '', $operator = '=')
    {
        $this->validateWhere($field, $value, $operator, $type = "AND");
        return $this;
    }

    /**
     * groupBy build group by statement
     *
     * @param  string $field
     *
     * @return object
     */
    public function groupBy($field)
    {
        $this->groupBy = ' GROUP BY ' . $field;
        return $this;
    }

    /**
     * orderBy build orderBy statement
     *
     * @param  string $field
     * @param  string $mode
     *
     * @return object
     */
    public function orderBy($field, $mode = 'ASC')
    {
        $this->orderBy = ' ORDER BY ' . $field . ' ' . $mode;
        return $this;
    }
    /**
     * validateSelectFields check if select fields are valid for specific table
     *                      and define safe select fields
     *                      for mysqli
     *                      for PDO override method
     *                      don't validate for joins(?)
     *
     * @param  string  $fields
     *
     * @return void
     */
    private function validateSelectFields($fields='')
    {
        if(empty($fields)) {
            $fields = '*';
        }
        if(!$this->validate) {
            $this->select = $fields;
            return;
        }
        if ($fields === '*') {
            $this->select = '*';
            if (!empty($this->tableFields)) {
                $this->select = implode(',', $this->tableFields);
            }
            return;
        }

        // TODO: issue with spacing in string
        $fields = preg_split('/ ?[,|] ?/', $fields);
        // 1
        // $v = explode(',', $fields);
        // $v = array_map('trim', $v);
        // 2
        // $keyword_array=explode(",", trim($fields));

        if (empty($fields)) {
            throw new Exception('Fields are empty');
        }

        $arr = [];
        foreach ($fields as $f) {
            if (!in_array($f, $this->tableFields)) {
                //just remove unsafe fields
                continue;
            }
            $arr[] = $f;
        }

        if (empty($arr)) {
            throw new Exception('Array is empty, no matching fields');
        }

        $this->select = implode(',', $arr);
    }

/*-------------------- end of trait ---------------*/
}
