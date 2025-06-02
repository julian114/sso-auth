<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

Route::get('/', [RoleController::class, 'index'])->name('roles');
Route::get('/get', [RoleController::class, 'get']);
Route::post('/create', [RoleController::class, 'create']);
Route::get('/edit/{id}', [RoleController::class, 'edit']);
Route::put('/update/{id}', [RoleController::class, 'update']);
Route::delete('/delete/{id}', [RoleController::class, 'delete']);
Route::put('/updateStatus/{id}', [RoleController::class, 'updateStatus']);
Route::get('/selectPermissions/{id}', [RoleController::class, 'selectPermissions']);
Route::post('/asigPermissions', [RoleController::class, 'asigPermissions']);
