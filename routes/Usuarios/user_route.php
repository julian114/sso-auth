<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index'])->name('usuario');
Route::get('/get', [UserController::class, 'get']);
Route::post('/create', [UserController::class, 'create']);
Route::get('/edit/{id}', [UserController::class, 'edit']);
Route::put('/update/{id}', [UserController::class, 'update']);
Route::delete('/delete/{id}', [UserController::class, 'delete']);
Route::put('/updateStatus/{id}', [UserController::class, 'updateStatus']);
Route::get('/selectRole/{id}', [UserController::class, 'selectRole']);
Route::post('/asigRole', [UserController::class, 'asigRole']);
