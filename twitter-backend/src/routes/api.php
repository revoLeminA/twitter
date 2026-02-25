<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('firebase.auth')->group(function () {
    Route::apiResource('/auth', AuthController::class);
    Route::apiResource('/posts', PostController::class);
    Route::patch('/posts/{post}/like', [LikeController::class, 'toggle']);
    Route::apiResource('/posts/{post}/comment', CommentController::class);
});
