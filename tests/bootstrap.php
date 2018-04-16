<?php // bootstrap.php

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
|
| load important files
| 1-config.php
| 2-vendor autoload
| 3-commond-dev.php - defined constants
|
*/

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

defined('EOL') ? null : define('EOL', PHP_EOL);

/*
|--------------------------------------------------------------------------
| Autoload
|--------------------------------------------------------------------------
*/
require_once dirname(__DIR__) . '/vendor/autoload.php';

require_once 'traits/ReflectionsTrait.php';
require_once 'traits/ManagementTrait.php';

$config = require_once 'config.php';

// Environment
if(!file_exists(__DIR__ . '/config.php')) {
    die(EOL.' Please check if /app/tests/config.php file exists, otherwise rename config.sample.php and change values as suggested'.EOL);
}

if(!file_exists((dirname(__DIR__)). '/.env.ini') && empty($config)) {
    die('DB configuration is missing');
}
if(file_exists((dirname(__DIR__)). '/.env.ini')) {
    $ini = parse_ini_file((dirname(__DIR__)). '/.env.ini');
    if(!empty($ini)) {
        if(is_array($config)) {
            $config = array_merge($config, $ini);
        }
        else {
            $config = $ini;
        }
    }
}

defined('CONTACT_EMAIL') ? null : define ('CONTACT_EMAIL', $config['contactEmail']);
defined('ENVIRONMENT') ? null : define('ENVIRONMENT', $config['environment']);
defined('DEBUG') ? null : define('DEBUG', $config['debug']);
/*
|--------------------------------------------------------------------------
| Other files to include
|--------------------------------------------------------------------------
*/
// require_once __DIR__ . '/config.php';
// $config = $config[gethostname()];
// print_r($config);

require_once dirname(__DIR__) .'/src/config/errorHandler.php';
require_once dirname(__DIR__) .'/src/config/exceptionHandler.php';



