<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
/**
 * Summary: methods needed for post-install and post update scripts in composer
 */
/**
 * Loader class- running install/update composer, scripts require some actions
 *               copying config file
 *               copying DB folder with examples of migrations from tests/data
 *               (just simple sql files)
 *
 * PHP version 5.6 / 7.0
 *
 * @package    DBLayer
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2018 Tereza Simcic
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       Loader.php
 * @usage:     with composer on install/update
 */

namespace DBLayer;

class Loader
{
    /**
     * postInstall - if composer.lock is present, otherwise postUpdate is
     *               running for both commands, post-install-cmd and
     *               post-update-cmd
     */
    public static function postInstall ()
    {
        (new self)->copyDirs();
        // echo ' it is postInstall method' . PHP_EOL;
        return;
    }

    /**
     * postUpdate
     */
    public static function postUpdate ()
    {
        (new self)->copyDirs();

        // echo ' it is postUpdate method' . PHP_EOL;
        return;
    }

    /**
     * create config dir
     * copy database directories from src
     * cleanup
     *
     * @return void
     */
    private function copyDirs()
    {
        echo 'Copying ... ' . PHP_EOL;


        $appDir = getcwd() . '/config';
        $pckFile = dirname(__FILE__).'/config/config.sample.php';
        // echo 'appDir' . $appDir . PHP_EOL;
        // echo 'pckFile' . $pckFile . PHP_EOL;

        // Create config dir
        if(!file_exists($appDir)) {
            mkdir($appDir);
        }
        // Copy config file
        if(file_exists($pckFile)) {
            if(!copy($pckFile ,$appDir.'/dblayer.php'))
            {
                $errors= error_get_last();
                echo "COPY ERROR: ";
                print_r($errors);
            } else {
                echo "Config file copied: ". $appDir .'/dblayer.php' . PHP_EOL;
                echo PHP_EOL . 'Please update credentials.' .PHP_EOL;
            }
        }

        $pckFile = dirname(dirname(__FILE__)).'/database/';
        if(!file_exists(getcwd() . '/database')) {
            shell_exec('cp -R ' . $pckFile . ' ' . getcwd() . '/database');
            // shell_exec('rm ' .  getcwd() . '/database/mappers/*');
        }

        echo PHP_EOL . ' You are ready to start ' . PHP_EOL;
        echo ' Please run from console: ';
        echo PHP_EOL . '---------------------------- ' . PHP_EOL;
        echo PHP_EOL . '  $ php DBLayer/generator ' . PHP_EOL;
        echo PHP_EOL . '---------------------------- ' . PHP_EOL;
        echo PHP_EOL . ' if you want to migrate and seed database tables, please run:  ' ;
        echo PHP_EOL . '---------------------------- ' . PHP_EOL;
        echo PHP_EOL . '  $ php DBLayer/migrator ' . PHP_EOL;
        echo PHP_EOL . '---------------------------- ' . PHP_EOL;
    }
}
