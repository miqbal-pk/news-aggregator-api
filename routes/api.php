<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ArticleController;
   
Route::controller(UserController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('reset-password', 'login');
});
         
Route::middleware('auth:sanctum')->group( function () {

    Route::controller(UserController::class)->group(function(){
        Route::post('logout', 'logout');
        Route::get('get-user-preferences', 'getUserPreferences');
        Route::post('set-user-preferences', 'setUserPreferences');
        Route::get('get-user-feeds', 'getUserFeeds');
    });

    Route::controller(ArticleController::class)->group(function(){
        Route::get('articles', 'index');
        Route::get('article/{id}', 'show');
        Route::get('articles/search', 'search');
        
    });

    // Route::get('articles', ArticleController::class);
});