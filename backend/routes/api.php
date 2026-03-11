<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/category-brand', [ProductController::class, 'categoryBrand']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/user/update/{id}',[MemberController::class, 'updateProfile']);
    Route::post('/user/product/add', [ProductController::class, 'addProduct']);
    Route::get('/user/my-product', [ProductController::class, 'myProduct']);

    Route::get('/user/product/delete/{id}', [ProductController::class, 'deleteProduct']);
});

Route::get('/product', [ProductController::class, 'product']);
Route::get('/product/detail/{id}', [ProductController::class, 'detail']);
Route::post('/product/cart',[ProductController::class, 'productCart']);
?>