<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('/users', UserController::class);

// Route::get('users', a[UserController::class, 'index']);
// Route::post('users', [UserController::class, 'store']);
// Route::get('users/{email}', [UserController::class, 'show']);
// Route::put('users/{email}', [UserController::class, 'update']);
// Route::delete('users/{emil}', [UserController::class, 'destroy']);
