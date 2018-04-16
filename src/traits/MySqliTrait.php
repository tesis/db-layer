<?php
//File: src/traits/MySqliTrait.php
/**
 * Summary: trait for database connection
 *          dependency: use ConnectDbTrait
 */
/**
 * MySqliTrait
 *
 * PHP version 5.6 / 7.0
 * @package    DBLayer
 * @subpackage MysqliLayer
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2018 Tereza Simcic <updated: 2018>
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       MySqliTrait.php
 *
 * can be used in any class
 * options:
 * 1 connection is already eastablished, pass the mysqli object
 * 2 new connection
 *     - pass an array with keys: host, user, pass, dbName
 *     - pass host, user, password,dbName
 *     - pass host, user, password - for migrations where
 *       we don't have dbName set yet
 *
 */

namespace Tesis\DBLayer\Traits;

use Exception;
use mysqli_sql_exception;
use mysqli;

trait MySqliTrait
{
    /**
     *  connect to database, for mysqli extension
     *
     * @param  string $dbHost
     * @param  string $dbUser
     * @param  string $dbPass
     * @param  string $dbName
     *
     * @return void
     */

    protected function connect($dbHost = '', $dbUser = '', $dbPass = '', $dbName = '')
    {
        $conn = null;

        try {
            $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
            if($conn->connect_error) {
                throw new mysqli_sql_exception( ' ERROR !!! ' . $conn->connect_error);
            }
            $conn->set_charset($this->dbCharset);

        } catch(mysqli_sql_exception $e) {
            $this->error = 'Mysqli server error: ' . $e->getMessage();
            $this->errorInfo['message'] = $this->error;
            $this->errorInfo['file'] = $e->getFile();
            $this->errorInfo['line'] = $e->getLine();
            $this->errorInfo['trace'] = $e->getTraceAsString();
            return false;
        } catch(Exception $e) {
            $this->error = 'Mysqli server error: ' . $e->getMessage();
            $this->errorInfo['message'] = $this->error;
            $this->errorInfo['file'] = $e->getFile();
            $this->errorInfo['line'] = $e->getLine();
            $this->errorInfo['trace'] = $e->getTraceAsString();
            return false;
        } finally {
            $this->conn = $conn;
            $this->database = $dbName;
        }

    }

    /**
     * selectDatabase
     *
     * @param string $dbName name of database to use
     */
    public function selectDatabase($dbName)
    {
        $this->conn->select_db($dbName);
    }

    /**
     * existingConnections check existing connections from database
     *
     * @return bool|int
     */
    public function existingConnections($conn)
    {
        $sql = "SHOW STATUS WHERE `variable_name` = 'Threads_connected';";
        $result = $conn->query($sql);

        if (!$result) {
            return false;
        }
        return count($result);
    }

    /**
     * describeTable
     */
    public function describeTable ($tableName = '')
    {
        if(empty($tableName)) {
            throw new InvalidArgumentException('Table name should not be empty');
        }
        if(is_null($this->conn)) {
            throw new mysqli_sql_exception('Connection not established');
        }

        $sql = "DESCRIBE " . $tableName;
        $res = $this->conn->query($sql);

        if(!$res) {
            throw new Exception('Error fetching data: ' . $this->conn->error);
        }
        return $res;
    }

/*-------------------- end of trait ---------------*/
}
