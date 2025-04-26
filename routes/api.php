<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 公共路由
Route ::post('login', [AuthController::class, 'login']);
Route ::post('register', [AuthController::class, 'register']);

// 受保护的路由
Route ::middleware('auth:api') -> group(function() {
    // 认证路由
    Route ::post('logout', [AuthController::class, 'logout']);
    Route ::get('user', [AuthController::class, 'user']);

    // 客户API路由
    Route ::apiResource('customers', CustomerController::class);
});
