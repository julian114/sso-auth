<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;

Route::get('/', [PermissionController::class, 'index'])->name('permisos');
Route::get('/get', [PermissionController::class, 'get']);
Route::post('/create', [PermissionController::class, 'create']);
Route::get('/edit/{id}', [PermissionController::class, 'edit']);
Route::put('/update/{id}', [PermissionController::class, 'update']);
Route::delete('/delete/{id}', [PermissionController::class, 'delete']);
Route::put('/updateStatus/{id}', [PermissionController::class, 'updateStatus']);
