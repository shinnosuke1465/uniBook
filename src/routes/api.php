<?php

use App\Platform\Presentations\User\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return response()->json([
        'message' => "This is a test message from API.",
    ],200);
});

//ユーザー作成
Route::post('/users', [UserController::class, 'create'])
->name('users.create');

Route::middleware('auth:sanctum')->group(function () {
    //ユーザー情報取得
    Route::get('/users/me', [UserController::class, 'me'])
    ->name('users.me');
});
