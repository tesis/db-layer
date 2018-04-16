<?php // /helpers.php

// Assign $e messages to class variables error and errorInfo
function assignExceptions ($object, Exception $e, $return = true)
{
    $object->error = $e->getMessage();
    $object->errorInfo['message'] = $object->error;
    $object->errorInfo['file'] = $e->getFile();
    $object->errorInfo['line'] = $e->getLine();
    $object->errorInfo['trace'] = $e->getTraceAsString();

    if($return === true) {
        return false;
    }
}