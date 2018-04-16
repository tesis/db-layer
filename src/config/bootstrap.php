<?php // bootstrap.php

/*
|--------------------------------------------------------------------------
| Bootstrap needed for migrator and generator
|--------------------------------------------------------------------------
|
| load important files
| 1-path constants - only for loader class or other classes not connected to
|                    DBLayer
| 2-vendor autoload
| 3-config.php
| 4-config constants
| 5-error handler
|
*/

/*
|--------------------------------------------------------------------------
| Autoload
|--------------------------------------------------------------------------
*/
require_once getcwd() .'/vendor/autoload.php';


/*
|--------------------------------------------------------------------------
| CONFIG and ENV file
|--------------------------------------------------------------------------
*/

defined('ENV_INI') ? null : define('ENV_INI', getcwd().'/.env.ini');

$config = require_once getcwd().'/config/dblayer.php';

if(!file_exists(ENV_INI) && empty($config)) {
    die('DB configuration is missing');
}
if(file_exists(ENV_INI)) {
    $ini = parse_ini_file(ENV_INI);
    if(!empty($ini)) {
        if(is_array($config)) {
            $config = array_merge($config, $ini);
        }
        else {
            $config = $ini;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Other constants from config
|--------------------------------------------------------------------------
*/
defined('CONTACT_EMAIL') ? null : define ('CONTACT_EMAIL', $config['contactEmail']);
defined('ENVIRONMENT') ? null : define('ENVIRONMENT', $config['environment']);
defined('DEBUG') ? null : define('DEBUG', $config['debug']);

/*
|--------------------------------------------------------------------------
| Error Handler
|--------------------------------------------------------------------------
*/
require_once 'errorHandler.php';
require_once 'exceptionHandler.php';







