<?php
//File: /src/traits/DbManagerTrait.php
/**
 * Summary: trait for database management: create database, delete database,
 *          create table, depends on mysqli extension
 *          using with init (Migration class) in conjunction with mysqli ext
 */
/**
 * DbManagerTrait
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
 * @name       DbManagerTrait.php
 *
 */

namespace Tesis\DBLayer\Traits;

use Exception;

trait DbManagerTrait
{
    /**
     * createDatabase
     *
     * @param  string $dbName
     *
     * @return boolean
     */
    public function createDatabase($dbName = '')
    {
        try {
            $this->validator($dbName);

            if ($this->checkDatabaseExists($dbName)) {
                $this->error = 'Database already created: ' . $dbName;
                return false;
            }

            $sql = 'CREATE DATABASE  ' . $dbName . ' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';

            if (!$this->conn->query($sql)) {
                $this->error = 'Database not created';
                $this->lastQuery = $sql;
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            $this->line = $e->getLine();
            return false;
        }
    }

    /**
     * validator
     */
    private function validator($dbName)
    {
        if (is_null($this->conn) || !$this->conn) {
            $this->error = 'Connection not established';
            throw new Exception($this->error);
        }
        if (empty($dbName)) {
            $this->error = 'Database name is missing';
            throw new Exception($this->error);
        }
    }

    /**
     * dropDatabase
     *
     * @param  string $dbName
     *
     * @return boolean
     */
    public function dropDatabase($dbName = '')
    {
        try {
            $this->validator($dbName);

            $sql = 'DROP DATABASE ' . $dbName;
            try {
                $this->conn->query($sql);
            } catch (Exception $e) {
                $this->error = __METHOD__ . ': ' . $e->getMessage();
                $this->line = $e->getLine();
                $this->lastQuery = $sql;
            }

        } catch (Exception $e) {
            $this->error = __METHOD__ . ': ' . $e->getMessage();
            $this->line = $e->getLine();
            return false;
        }
    }

    /**
     * checkDatabaseExists - using mysqli extension
     */
    public function checkDatabaseExists($dbName = '')
    {
        $this->validator($dbName);

        $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $dbName . "'";

        $res = $this->conn->query($sql);

        if ($res->num_rows === 0) {
            $this->error = 'Database not found: ' . $dbName;
            return false;
        }
        $this->error = 'Database found: ' . $dbName;
        return true;
    }

    /**
     * compareConfigs
     */
    public function compareConfigs($config)
    {
        if(!file_exists(ENV_INI)) {
            $this->writeIni($config, ENV_INI);
        }

        $configIni = parse_ini_file(ENV_INI);

        $msg = '';

        if (!empty($config) && !empty($configIni)) {
            $comp = array_diff($config, $configIni);

            $required = ['dbHost', 'dbPass', 'dbUser', 'dbName'];

            if (empty($comp)) {
                $msg .= 'ini file is OK ' . PHP_EOL;
            } else {
                foreach ($required as $req) {
                    if (!array_key_exists($req, $configIni) || $configIni[$req] === '') {
                        $msg .= $req . ' should be provided ' . PHP_EOL;
                        // Rewrite a file
                        $msg .= $this->writeFirstIni($config);
                    }
                }
                $msg .= 'ini file is OK ' . PHP_EOL;
            }
        } else {
            // Create ini file
            $msg .= $this->writeFirstIni($config);
        }

        return $msg;
    }
    /**
     * write config ini file after database is created,
     * holding database credentials
     *
     * @param  array|null $config
     *
     * @return
     */
    public function writeFirstIni(array $config = null)
    {
        $msg = "Start writing to INI file: " . PHP_EOL;
        if ($config !== null) {
            $this->config = $config;
        }

        if (empty($this->config)) {
            $this->error = ' Config array is missing ';
            return false;
        }

        // Create ini file
        if (!array_key_exists('dbName', $this->config)) {
            $this->config['dbName'] = $this->database;
        }
        $this->writeIni($this->config, ENV_INI);
        $msg .= "\tDONE: " . PHP_EOL . "\tCheck your configuration file: " . ENV_INI . PHP_EOL;

        return $msg;
    }
    /**
     * writeIni
     *
     * @param  array|null $config
     * @param  string     $iniFilename path to ini file
     *
     * @return
     */
    public function writeIni(array $config = null, $iniFilename = '.env.ini')
    {

        if (empty($config)) {
            $this->error = ' Config array is missing ';
            return false;
        }

        // Create ini file
        $content = '';

        foreach ($config as $key => $value) {
            $content .= $key . " = \"" . $value . "\"\n";
        }

        file_put_contents($iniFilename, $content);
        return true;
    }

    /**
     * generateMapper
     */
    public function generateMapper($config, $type = '')
    {
        if (empty($type)) {
            $type = $this->type;
        }
        $gen = new \Tesis\DBLayer\System\SqlGenerator($config);

        $tables = $gen->getAllTables($type);
        return $gen->tables;
    }

    /**
     * migrate
     */
    public function migrate($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception('Migration file does not exist: ' . $filename);
        }
        $content = include $filename;
        if(empty($content)) {
            throw new Exception('Migration file is empty: ' . $filename);
        }
        foreach ($content as $key => $sqls) {
            foreach ($sqls as $sql) {
                if (empty($sql)) {
                    $this->error = 'Empty SQL';
                    continue;
                }

                if (!$this->conn->query($sql)) {
                    $this->error = 'Create table error: ' . $this->conn->error;
                    $this->lastQuery = $sql;
                    throw new Exception($this->error);
                }
            }
        }
    }

    /**
     * seed
     */
    public function seed($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception('Seeder File does not exist: ' . $filename);
        }
        $count = 0;
        $content = include $filename;
        if(empty($content)) {
            throw new Exception('Seeder file is empty: ' . $filename);
        }
        foreach ($content as $key => $sqls) {
            foreach ($sqls as $sql) {
                if (empty($sql)) {
                    $this->error = 'Empty SQL';
                    continue;
                }

                if (!$this->conn->query($sql)) {
                    $this->lastQuery = $sql;
                    $this->error = 'Insert record error: ' . $this->conn->error;
                    throw new Exception($this->error);
                }
                $count++;

            }
        }

        return $count;
    }

/*-------------------- end of trait ---------------*/
}
