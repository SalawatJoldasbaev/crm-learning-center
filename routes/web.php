<?php

use Illuminate\Support\Facades\Route;

Route::get('/{route?}', function () {
    return view('welcome');
})->where('route', '^((?!api).)*$');
