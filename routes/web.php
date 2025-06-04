<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
//use App\Http\Controllers\Auth\MicrosoftController;

/*--------------------------------------------------------------------------
| Index                                                                    |
|--------------------------------------------------------------------------*/
Route::get('/', [PublicController::class, 'index'])->name('/');
Route::post('/login', [PublicController::class, 'login'])->name('login');
Route::post('/logout', [PublicController::class, 'destroy'])->middleware('auth')->name('logout');

/*--------------------------------------------------------------------------
| Microsoft OAuth                                                          |
|--------------------------------------------------------------------------*/
//Route::get('/login/microsoft', [MicrosoftController::class, 'redirectToMicrosoft'])->name('microsoft.login');
//Route::get('/login/microsoft/callback', [MicrosoftController::class, 'handleMicrosoftCallback'])->name('microsoft.callback');

/*--------------------------------------------------------------------------
| Dashboard                                                                 |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Dashboard'])->prefix('Dashboard')->group(function () {
    require_once __DIR__ . '/Dashboard/dashboard_route.php';
});


/*------------------------------------------------------------------------------------------
| --------- ADMINISTRACIÓN  ---------                                                      |
|-----------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
| Usuarios                                                                 |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Usuario'])->prefix('Usuario')->group(function () {
    require_once __DIR__ . '/Usuarios/user_route.php';
});

/*--------------------------------------------------------------------------
| Rol                                                                      |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Rol'])->prefix('Rol')->group(function () {
    require_once __DIR__ . '/Roles/role_route.php';
});

/*--------------------------------------------------------------------------
| Permisos                                                                 |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'RolePermissionUser', 'can:Modulo_Permiso'])->prefix('Permiso')->group(function () {
    require_once __DIR__ . '/Permisos/permission_route.php';
});

/*--------------------------------------------------------------------------
| Logs                                                                      |
|--------------------------------------------------------------------------*/
Route::middleware(['auth', 'can:Modulo_Log'])->prefix('Logs')->group(function () {
    require_once __DIR__ . '/Logs/logs_route.php';
});

