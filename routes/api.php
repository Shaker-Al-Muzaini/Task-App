<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;






Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/projects', [ProjectController::class,'store']);
Route::put('/projects/{id}', [ProjectController::class, 'update']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::post('/projects/pinned', [ProjectController::class, 'pinnedProject']);


//Members
Route::Get('/members', [MemberController::class,'index']);
Route::post('/members', [MemberController::class,'store']);
Route::put('/members/{id}', [MemberController::class, 'update']);


//Tasks
Route::post('/tasks', [TaskController::class,'createTask']);








