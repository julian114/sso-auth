<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MicrosoftAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// La ruta de verificación de token se mantiene en API
//Route::post('/microsoft/verify-token', [MicrosoftAuthController::class, 'verifyToken']);

Route::post('/microsoft/auth-sso-url', [App\Http\Controllers\Api\MicrosoftAuthController::class, 'getAuthUrl']);

Route::get('/microsoft/callback-sso-url', [App\Http\Controllers\Api\MicrosoftAuthController::class, 'handleCallback'])->name('microsoft.callbackSsoUrl');

