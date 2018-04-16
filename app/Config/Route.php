<?php

use Router\Route;

// Get
Route::get('/', 'HomeController::index');
Route::get('/plano-de-estudo', 'PlanoDeEstudoController::index');
Route::get('/historico', 'HistoricoController::index');
Route::get('/logout', 'AuthController::logout');

// Post
Route::post('/login', 'AuthController::login');
