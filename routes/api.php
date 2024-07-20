<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\ArticleController ;
use App\Http\Resources\ArticleCollection;
use App\Http\Controllers\API\v2\ArticleController as ArticleController2;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user',function(Request $request){
        return $request->user();
    });

    Route::post('logout',[\App\Http\Controllers\API\Auth\AuthController::class,'logout']);

});
Route::post('register',[\App\Http\Controllers\API\Auth\AuthController::class,'register']);
Route::post('login',[\App\Http\Controllers\API\Auth\AuthController::class,'login']);

Route::prefix('v1')->group(function(){
    Route::get('articles', [ArticleController::class, 'index']);
    Route::post('articles', [ArticleController::class, 'store']);
    Route::get('articles/search', [ArticleController::class, 'index']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);
    Route::put('articles/{id}', [ArticleController::class, 'update']);
    Route::delete('articles/{id}', [ArticleController::class, 'destroy']);
});

Route::prefix('v2')->group(function(){
    Route::get('articles', [App\Http\Controllers\API\v2\ArticleController::class, 'index']);
    Route::resource('articles',App\Http\Controllers\API\v2\ArticleController::class);
});

