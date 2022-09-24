<?php

function resSuccess($obj)
{
    $obj->success = true;

    $JSON = json_encode($obj);

    echo $JSON;

    exit();
}

function resFail($message, $errorCode = 400)
{
    $errorObj = new \stdClass();
    $errorObj->success = false;
    $errorObj->_error = $message;

    $JSON = json_encode($errorObj);

    echo $JSON;

    exit();
}
