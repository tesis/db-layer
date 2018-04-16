<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
/**
 * Summary: SqlGenerator class provides mapping of all tables: fields,PK,AI
 */
/**
 * BaseModel class- class for databases
 *                   (dealing with select/insert/update/delete sql statements)
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

namespace Tesis\DBLayer\System;

use Exception;
use Tesis\DBLayer\Traits\DbManagerTrait;
use Tesis\DBLayer\Traits\ConnectDbTrait;
use Tesis\DBLayer\Traits\MySqliTrait;

// require_once dirname(__DIR__) . '/config/bootstrap.php';

class Migrations
{
    use ConnectDbTrait, MySqliTrait, DbManagerTrait;

    /**
     * @access public
     * @var obj
     */
    public $conn = null;
    /**
     * database name
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
     * @var string
     */
    public $sql = '';
    /**
     * @access protected
     * @var array
     */

    public $tableName;
    /**
     * @access public
     * @var string
     */
    public $action = '';
    /**
     * @access public
     * @var integer
     */
    public $affectedRows = null;
    /**
     * @var string
     */
    public $dbDriver = 'mysqli';
    /**
     * @var array
     */
    public $config = [];
    /**
     * @var string
     */
    public $type = 'json';

    /**
     * __construct establish connection if is null
     */
    public function __construct($config = null)
    {
        try {
            $this->config = $config;
            $this->openConnection($config);
        } catch (PDOException $e) {
            assignExceptions($this, $e, false);
            $this->conn = null;
        } catch (Exception $e) {
            assignExceptions($this, $e, false);
            $this->conn = null;
        }
    }

    /*---------------- End of class ----------------------*/
}
