<?php

use App\Platform\Presentations\Authenticate\Controllers\AuthenticateController;
use App\Platform\Presentations\AuthenticateToken\Controllers\AuthenticateTokenController;
use App\Platform\Presentations\Image\Controllers\ImageController;
use App\Platform\Presentations\User\Controllers\UserController;
use App\Platform\Presentations\University\Controllers\UniversityController;
use App\Platform\Presentations\Faculty\Controllers\FacultyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return response()->json([
        'message' => "This is a test message from API.",
    ],200);
});

//ログイン
Route::post('/tokens/create', [AuthenticateTokenController::class, 'create'])->name('tokens.create');

//ユーザー作成
Route::post('/users', [UserController::class, 'create'])
->name('users.create');

//画像
Route::apiResource(
    '/images',
    ImageController::class
)->only(['index','show', 'store'])->names([
    'index' => 'images.index',
    'show' => 'images.show',
    'store' => 'images.store',
])
    ->parameters([
        'images' => 'imageIdString',
    ])
    ->whereUuid('imageIdString');

//大学
Route::apiResource(
    '/universities',
    UniversityController::class
)->only(['index','show', 'store'])->names([
    'index' => 'universities.index',
    'show' => 'universities.show',
    'store' => 'universities.store',
])
    ->parameters([
        'universities' => 'universityIdString',
    ])
    ->whereUuid('universityIdString');

//学部
Route::apiResource(
    '/faculties',
    FacultyController::class
)->only(['index','show', 'store'])->names([
    'index' => 'faculties.index',
    'show' => 'faculties.show',
    'store' => 'faculties.store',
])
    ->parameters([
        'faculties' => 'facultyIdString',
    ])
    ->whereUuid('facultyIdString');

Route::middleware('auth:sanctum')->group(function () {
    //ログアウト
    Route::post('/logout', [AuthenticateController::class, 'logout'])->name('logout');
    //ユーザー情報取得
    Route::get('/users/me', [UserController::class, 'me'])->name('users.me');
});
