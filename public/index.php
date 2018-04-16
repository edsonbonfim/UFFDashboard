<?php

session_start();

function exception_handler($exception)
{
    die("{$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}");
}

set_exception_handler('exception_handler');

chdir(dirname(__DIR__));

include getcwd() . '/bootstrap/app.php';
