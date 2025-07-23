<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 公共API路由（不需要认证）
Route::prefix('api')->group(function () {
    Route::get('textbook-versions/options', [App\Http\Controllers\Api\TextbookVersionController::class, 'options']);
});
