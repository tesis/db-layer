<?php

/*--------------------------------
| ERROR HANDLING
|--------------------------------*/

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);


ini_set('log_errors', '1');

$filename = dirname(__DIR__) . '/error.log';
ini_set('error_log', $filename);
// error_log('here I am');

/**
 * Used for logging all php notices,warings and etc in a file when error reporting
 * is set and display_errors is off
 * @uses used in prod env for logging all type of error of php code in a file for further debugging
 * and code performance
 */
define('ERROR_LOG_FILE', $filename);

/**
 * Custom error handler
 * @param integer $code
 * @param string $description
 * @param string $file
 * @param interger $line
 * @param mixed $context
 * @return boolean
 */
function handleError($code, $description, $file = null, $line = null, $context = null)
{
    $displayErrors = ini_get("display_errors");
    $displayErrors = strtolower($displayErrors);
    if (error_reporting() === 0 || $displayErrors === "on") {
        return false;
    }

    list($error, $log) = mapErrorCode($code);

    $data = array(
        'level' => $log,
        'code' => $code,
        'error' => $error,
        'description' => $description,
        'file' => $file,
        'line' => $line,
        'path' => $file,
        'message' => $error . ' (' . $code . '): ' . $description . ' in [' . $file . ', line ' . $line . ']',
    );

    if($data['error'] !== 'Notice') {
        // Add extended data
        // $data['context'] = $context;
    }

    if(DEBUG === true) {
        return fileLog($data);
    }
    // if debug = false, ie. on production, send email
    // error_log ($data['message'], 1, CONTACT_EMAIL);
    if( ($data['error'] !== 'Notice') && ($code < 2048)){
        // echo '<div class="error"> A system error occurred. We apologize for the inconvenience.</div>';
    }
}

/**
 * This method is used to write data in file
 * @param mixed $logData
 * @param string $fileName
 * @return boolean
 */
function fileLog($logData, $fileName = ERROR_LOG_FILE)
{
    $fh = fopen($fileName, 'a+');
    if (is_array($logData)) {
        $logData = print_r($logData, true);
    }
    $status = fwrite($fh, $logData);
    fclose($fh);
    return ($status) ? true : false;
}

/**
 * Map an error code into an Error word, and log location.
 *
 * @param int $code Error code to map
 * @return array Array of error word, and log location.
 */
function mapErrorCode($code)
{
    $error = $log = null;
    switch ($code) {
        case E_PARSE:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $error = 'Fatal Error';
            $log = LOG_ERR;
            break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
        case E_RECOVERABLE_ERROR:
            $error = 'Warning';
            $log = LOG_WARNING;
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            $log = LOG_NOTICE;
            break;
        case E_STRICT:
            $error = 'Strict';
            $log = LOG_NOTICE;
            break;
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $error = 'Deprecated';
            $log = LOG_NOTICE;
            break;
        default:
            break;
    }
    return array($error, $log);
}

// Calling custom error handler
set_error_handler("handleError");

// Trigger Error
// trigger_error('triggering error', E_USER_NOTICE);

/*--------------------------------------------------------------------------
| DEBUGGING TOOLS
| Write errors on the page - for some issue, log them into file
| $type can be anything: info, regular, fatal, ..., to distinguish logs
|--------------------------------------------------------------------------*/
function debugLog($message = '', $log = true, $type = 'info')
{
    $filename = ERROR_LOG_FILE;

    if (empty($message)) {
        //$message = 'an issue occured - unknonwn';
        return false;
    }
    $message = '[ERROR ' . $type . ': ' . date('d/m/Y H:i:s') . ']' . ' ' . PHP_EOL . $message . PHP_EOL;
    // if (ENVIRONMENT == 'dev') {
    //if(file_exists($filename) && is_writable($filename) && $log === true) {
    if (file_exists($filename)) {
        file_put_contents($filename, $message, FILE_APPEND);
    }
    // }
}
