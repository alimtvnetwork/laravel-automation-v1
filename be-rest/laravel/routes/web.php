<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'status' => 'ok',
    'data' => ['service' => 'be-rest/laravel', 'note' => 'scaffold only'],
    'error' => null,
]));
