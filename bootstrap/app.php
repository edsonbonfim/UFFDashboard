<?php

include getcwd() . '/vendor/autoload.php';

use Pizza\App;

$app = new App();

$app->register(Pizza\View::class);
$app->register(Pizza\Route::class);
$app->register(Pizza\Database::class);

$app->handle();
