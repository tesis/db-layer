<?php
//File: src/traits/PDOTrait.php
/**
 * Summary: trait for database connection using PDO class
 *          dependency: use ConnectDbTrait
 */
/**
 * PDOTrait
 *
 * PHP version 5.6 / 7.0
 *
 * @package    DBLayer
 * @subpackage PDOlayer
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2018 Tereza Simcic <updated: 2018>
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       PDOTrait.php
 *
 */
namespace Tesis\DBLayer\Traits;

use Exception;
use PDOException;
use PDO;

trait PDOTrait
{
    /**
     * connect to database, use PDO extension
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
            $dns = 'mysql:host=' . $dbHost . ';dbname=' . $dbName;
            $options = [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                //  Some drivers do not support native prepared statements or have limited support for them
                // When you switch databases like that it appears to force PDO to emulate prepared statements. Setting PDO::ATTR_EMULATE_PREPARES to false and then trying to use another database will fail.
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->dbCharset,
            ];
            $conn = new PDO($dns, $dbUser, $dbPass, $options);
            $this->database = $dbName;
        } catch (PDOException $e) {
            assignExceptions($this, $e);
        } catch (Exception $e) {
            assignExceptions($this, $e);
        } finally {
            $this->conn = $conn;
        }

    }

    /**
     * selectDatabase
     *
     * @param string $dbName name of database to use
     */
    public function selectDatabase($dbName)
    {
        // $this->conn->query("create database " . $database);

        // $this->conn->query('use ' . $database);
        $this->conn->exec('use ' . $dbName);
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
