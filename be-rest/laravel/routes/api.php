<?php

declare(strict_types=1);

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('students', [StudentController::class, 'index']);
Route::get('students/{id}', [StudentController::class, 'show'])->whereNumber('id');
Route::post('students', [StudentController::class, 'store']);
Route::put('students/{id}', [StudentController::class, 'update'])->whereNumber('id');
Route::patch('students/{id}', [StudentController::class, 'update'])->whereNumber('id');
Route::delete('students/{id}', [StudentController::class, 'destroy'])->whereNumber('id');

Route::post('file-upload', [FileController::class, 'upload']);
Route::get('file-serve/{id}', [FileController::class, 'serve'])->whereNumber('id');
