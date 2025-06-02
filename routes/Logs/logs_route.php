<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('logs');
