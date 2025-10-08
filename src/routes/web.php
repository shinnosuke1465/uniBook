<?php

use Illuminate\Support\Facades\Route;

// ヘルスチェック用エンドポイント（ALB用）
Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});
