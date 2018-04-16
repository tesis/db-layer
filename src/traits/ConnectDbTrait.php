<?php
//File: src/traits/ConnectDbTrait.php
/**
 * Summary: trait for database connection used in conjunction with
 *                                        mysqli or pdo trait,
 *
 */
/**
 * ConnectDbTrait
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
 * @name       ConnectDbTrait.php
 *
 * can be used in any class
 * options:
 * 1 connection is already eastablished, pass the mysqli/pdo layer object
 * 2 new connection
 *     - pass an array with keys: host, user, pass, dbName
 *     - pass host, user, password,dbName
 *     - pass host, user, password - for migrations where
 *       we don't have dbName set yet
 *
 */

namespace Tesis\DBLayer\Traits;

use Exception;
use Tesis\DBLayer\Exceptions\ConnectionException;

trait ConnectDbTrait
{
    /**
     * openConnection
     * @param  obj $conn
     * @return obj
     */
    public function openConnection($dbHost = null, $dbUser = null, $dbPass = null, $dbName = null, $dbCharset = 'utf8')
    {
        $this->conn = null;
        $this->date = date('m/d/Y H:i:s');
        $this->dbCharset = $dbCharset;

        try {
            if (is_null($dbHost) || empty($dbHost)) {
                throw new ConnectionException("Connection not established. Host, or config data are missing");
            }
            if (is_object($dbHost) && get_class($dbHost) === $this->dbDriver) {
                $this->conn = $dbHost;
            }
            // if params passed as an array
            elseif (is_array($dbHost)) {
                foreach ($dbHost as $key => $val) {
                    $$key = $val;
                }
                $this->connect($dbHost, $dbUser, $dbPass, $dbName);
            } else {
                $this->connect($dbHost, $dbUser, $dbPass, $dbName);
            }
        } catch (ConnectionException $e) {
            $this->error = $e->getMessage();
            $this->errorInfo['message'] = $this->error;
            $this->errorInfo['file'] = $e->getFile();
            $this->errorInfo['line'] = $e->getLine();
            $this->errorInfo['trace'] = $e->getTraceAsString();
            return false;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->errorInfo['message'] = $this->error;
            $this->errorInfo['file'] = $e->getFile();
            $this->errorInfo['line'] = $e->getLine();
            $this->errorInfo['trace'] = $e->getTraceAsString();
            return false;
        }
    }

    /**
     * closeConnection
     *
     * @access private
     *
     * @return void
     *
     */
    public function closeConnection($conn)
    {
        $this->conn = null;
    }

    /**
     * setDatabase set database, change default database to different one
     *
     * @param string $db
     */
    public function setDatabase($database = null)
    {
        try {
            if (is_null($this->conn)) {
                throw new ConnectionException("Connection not established, check your credentials");
            }
            if (is_null($database) || empty($database)) {
                throw new Exception("Database not selected");
            }
            $this->selectDatabase($database);

            $this->database = $database;
        } catch (ConnectionException $e) {
            $this->error = $e->getMessage();
            $this->errorInfo['message'] = $this->error;
            $this->errorInfo['file'] = $e->getFile();
            $this->errorInfo['line'] = $e->getLine();
            $this->errorInfo['trace'] = $e->getTraceAsString();
            return false;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->errorInfo['message'] = $this->error;
            $this->errorInfo['file'] = $e->getFile();
            $this->errorInfo['line'] = $e->getLine();
            $this->errorInfo['trace'] = $e->getTraceAsString();
            return false;
        }
    }

/*-------------------- end of trait ---------------*/
}
