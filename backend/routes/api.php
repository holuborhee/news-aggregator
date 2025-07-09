<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\MetadataController;

Route::prefix('v1')->group(function () {

    // 🔓 Public Routes
    Route::get('articles', [ArticleController::class, 'search']);

    Route::get('metadata', [MetadataController::class, 'all']);

    // 🔐 Auth routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // 🔐 Protected (authenticated) Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('preferences', [PreferenceController::class, 'show']);
        Route::put('preferences', [PreferenceController::class, 'update']);
        Route::get('feed', [FeedController::class, 'index']);
    });
});