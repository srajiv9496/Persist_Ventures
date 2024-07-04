<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'index']);
Route::post('/uploadFile', [PageController::class, 'uploadFile']);
Route::get('/users', [PageController::class, 'showUsers']);
Route::get('/backup', [PageController::class, 'backupDatabase'])->middleware('auth:api', 'admin');

