<?php // /config/exceptionHandler.php

function handleUncaughtException($e, $write = false){
    // Display generic error message to the user
    echo "Ups! Something went wrong." . PHP_EOL;

    // Construct the error string
    $error = "Uncaught Exception: " . date("Y-m-d H:i:s") . PHP_EOL;
    $error .= $e->getMessage() . PHP_EOL;
    $error .= "File: " . $e->getFile() . " line: " . $e->getLine() . PHP_EOL;

    echo $error;

    // Log details of error in a file
    if(function_exists('debugLog') && $write === true) {
        debugLog($error);
    }
}

// Register custom exception handler
set_exception_handler("handleUncaughtException");
