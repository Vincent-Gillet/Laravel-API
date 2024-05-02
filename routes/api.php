<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use function Ramsey\Uuid\v1;

use App\Http\Controllers\ProductApiController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\Controller;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::prefix('v1')->group(function () {

//     Route::apiResource('users', UserApiController::class)->middleware('auth:sanctum');
//     Route::post('/register', [UserApiController::class, 'createUser'])->middleware('auth:sanctum');
//     Route::post('/login', [UserApiController::class, 'loginUser'])->middleware('auth:sanctum');

//     Route::apiResource('products', ProductApiController::class)->middleware('auth:sanctum');
// });



Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
        // Route::get('/welcome', function (Request $request) {
        //     return 'Stock de produit';
        // });
    
        // Route::get('/users', function (Request $request) {
        //     $users = User::all();
        //     return response()->json($users);
        // }); 
        // Route::get('/users', function (Request $request) {
        //     $users = User::all();
        //     return response()->json($users);
        // }); 
        Route::apiResource('users', UserApiController::class);
        Route::post('/register', [UserApiController::class, 'createUser']);
        Route::post('/login', [UserApiController::class, 'loginUser']);
    
        Route::apiResource('products', ProductApiController::class);
});


Route::get('/hello', function () {
    return "Hello World!";
});
