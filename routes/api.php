<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;






Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/projects', [ProjectController::class,'store']);
Route::put('/projects/{id}', [ProjectController::class, 'update']);


