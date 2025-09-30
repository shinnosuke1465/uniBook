<?php

use App\Platform\Presentations\Authenticate\Controllers\AuthenticateController;
use App\Platform\Presentations\AuthenticateToken\Controllers\AuthenticateTokenController;
use App\Platform\Presentations\DealRoom\Controllers\DealRoomController;
use App\Platform\Presentations\Image\Controllers\ImageController;
use App\Platform\Presentations\Textbook\Controllers\TextbookController;
use App\Platform\Presentations\TextbookDeal\Controllers\TextbookDealController;
use App\Platform\Presentations\User\Controllers\UserController;
use App\Platform\Presentations\University\Controllers\UniversityController;
use App\Platform\Presentations\Faculty\Controllers\FacultyController;
use App\Platform\Presentations\User\Me\Controllers\GetLikedTextbooksController;
use App\Platform\Presentations\User\Me\Controllers\GetListedTextbooksController;
use App\Platform\Presentations\User\Me\Controllers\GetPurchasedProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Platform\Presentations\Comment\Controllers\CommentController;
use App\Platform\Presentations\Like\Controllers\LikeController;

Route::get('/test', function (Request $request) {
    return response()->json([
        'message' => "This is a test message from API.",
    ],200);
});

//ログイン
Route::post('/login', [AuthenticateTokenController::class, 'create'])->name('tokens.create');

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

//画像
Route::apiResource(
    '/textbooks',
    TextbookController::class
)->only(['index','show', 'store', 'update', 'destroy'])->names([
    'index' => 'textbooks.index',
    'show' => 'textbooks.show',
    'store' => 'textbooks.store',
    'update' => 'textbooks.update',
    'destroy' => 'textbooks.destroy',
])
    ->parameters([
        'textbooks' => 'textbookIdString',
    ])
    ->whereUuid('textbookIdString');

//コメント
Route::apiResource(
    '/textbooks/{textbookId}/comments',
    CommentController::class
)->only(['store'])->names([
    'store' => 'comments.store',
])
    ->parameters([
        'textbooks' => 'textbookIdString',
    ])
    ->whereUuid('textbookIdString');

//いいね
Route::apiResource(
    '/textbooks/{textbookId}/likes',
    LikeController::class
)->only(['store', 'destroy'])->names([
    'store' => 'likes.store',
    'destroy' => 'likes.destroy',
])
    ->parameters([
        'textbooks' => 'textbookIdString',
    ])
    ->whereUuid('textbookIdString');

//いいねした教科書一覧取得
Route::apiResource(
    '/me/likes',
    GetLikedTextbooksController::class
)->only(['index'])->names([
    'index' => 'me.likes',
]);

//購入商品取得
Route::apiResource(
    '/me/purchased_textbooks',
    GetPurchasedProductsController::class
)->only(['index', 'show'])->names([
    'index' => 'me.purchased_textbooks',
    'show' => 'me.purchased_textbooks.show',
])
    ->parameters([
        'purchased_textbooks' => 'textbookIdString',
    ])
    ->whereUuid('textbookIdString');

//出品商品取得
Route::apiResource(
    '/me/listed_textbooks',
GetListedTextbooksController::class
)->only(['index', 'show'])->names([
    'index' => 'me.listed_textbooks',
    'show' => 'me.listed_textbooks.show',
])
    ->parameters([
        'listed_textbooks' => 'textbookIdString',
    ])
    ->whereUuid('textbookIdString');

//取引ルーム一覧
Route::apiResource(
    '/me/dealrooms',
    DealRoomController::class
)->only(['index'])->names([
    'index' => 'dealrooms.index',
]);

Route::post('/textbooks/{textbookId}/deal/payment_intent',
    [TextbookDealController::class, 'createPaymentIntent']
)->middleware('auth:sanctum')->name('textbooks.deals.payment-intent.store');

Route::post('/textbooks/{textbookId}/deal/payment_intent/verify',
    [TextbookDealController::class, 'verifyPaymentIntent']
)->middleware('auth:sanctum')->name('textbooks.deals.payment-intent.verify.store');

Route::post('/textbooks/{textbookId}/deal/cancel',
    [TextbookDealController::class, 'cancel']
)->middleware('auth:sanctum')->name('textbooks.deals.cancel');

Route::post('/textbooks/{textbookId}/deal/report_delivery',
    [TextbookDealController::class, 'reportDelivery']
)->middleware('auth:sanctum')->name('textbooks.deals.reportDelivery');

Route::post('/textbooks/{textbookId}/deal/report_receipt',
    [TextbookDealController::class, 'reportReceipt']
)->middleware('auth:sanctum')->name('textbooks.deals.reportReceipt');


Route::middleware('auth:sanctum')->group(function () {
    //ログアウト
    Route::post('/logout', [AuthenticateController::class, 'logout'])->name('logout');
    //ユーザー情報取得
    Route::get('/users/me', [UserController::class, 'me'])->name('users.me');
});
