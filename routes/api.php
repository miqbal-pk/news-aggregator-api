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
    });

    Route::resource('articles', ArticleController::class);
});