<?php

namespace PHPUnitTests\Traits;

trait ManagementTrait
{
    protected function setVerboseErrorHandler()
    {
        $handler = function ($errorNumber, $errorString, $errorFile, $errorLine) {
            echo "
          MyTESTS ERROR INFO Handler
          Message: $errorString
          File: $errorFile
          Line: $errorLine
          ";
        };
        set_error_handler($handler);
    }

    protected function formatBytes($bytes, $precision = 2)
    {
        $kilobyte = 1024;
        $megabyte = 1024 * 1024;
        if ($bytes >= 0 && $bytes < $kilobyte) {
            return $bytes . " b";
        }
        if ($bytes >= $kilobyte && $bytes < $megabyte) {
            return round($bytes / $kilobyte, $precision) . " kB";
        }
        // return round($bytes / $megabyte, $precision) . " MB";
        return round($bytes / $kilobyte, $precision) . " kB";
    }

    /**
     * memoryAllocation
     * interested in VmRSS (Virtual Memory Resident Set Size) => allocated
     *  memory  by the process which resides in the main memory (RAM)
     *  and in swap space - VmSwap
     */
    protected function memoryAllocation ()
    {
        $status = file_get_contents('/proc/' . getmypid() . '/status');

        // print $status . EOL;

        $matchArr = [];
        preg_match_all('~^(VmRSS|VmSwap):\s*([0-9]+).*$~im', $status, $matchArr);

        if(!isset($matchArr[2][0]) || !isset($matchArr[2][1])) {
            return false;
        }

        echo EOL . 'memory_get_usage: ' . $this->formatBytes(memory_get_usage()) . EOL;
        echo 'memory_get_usage T: ' . $this->formatBytes(memory_get_usage(true)) . EOL;
        echo 'memory_get_peak_usage: ' . $this->formatBytes(memory_get_peak_usage()) . EOL;
        echo 'memory_get_peak_usage T: ' . $this->formatBytes(memory_get_peak_usage(true)) . EOL;

        return intval($matchArr[2][0]) + intval($matchArr[2][1]) . ' kB';
    }

    protected function output($text, $method=true)
    {
        if($method === false) {
            fwrite(STDOUT, EOL. $text. EOL);
        }
        else {
            fwrite(STDOUT, EOL.EOL .'*** METHOD: '.$text. '***' .EOL.
                '--------------------------------------------------------------'. EOL);
        }
    }

    protected function writeToFile($text)
    {
        global $config;

        if(file_exists($path = $config['path_tests_stdout'])) {
            if(is_string($text)) {
                file_put_contents($path, $text);
            }
            else{
                file_put_contents($path, print_r($text, true));
            }
            // write out file size, ... any other params
            fwrite(STDOUT, EOL.EOL .'>>>> output written to the file ' .EOL);
            return ;
        }

        fwrite(STDOUT, EOL.EOL .'>>>> output written to the file not written - check file_path! ' .EOL);
    }
    /**
     * test time, wallclock time
     * $dat = getrusage();
     * ru_utime.tv_usec] => 36000 user time used (ms)
     *  [ru_stime.tv_usec] => 8000 system time used (ms)
     *  > PHP 5.4.0:
     *  $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
     */
    protected function startTime()
    {
        return microtime(true);
    }

    protected function endTime($starttime, $round=6)
    {
        return round(microtime(true) - $starttime,$round);
    }
}
