<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CategoryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// forgot / reset password (ported from blade Frontend)
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

// search (ported from blade Frontend\ProductController)
Route::get('/search',          [ProductController::class, 'search']);
Route::get('/advanced-search', [ProductController::class, 'advancedSearch']);
Route::get('/filter-price',    [ProductController::class, 'filterPrice']);

Route::get('/category-brand', [ProductController::class, 'categoryBrand']);
// cây danh mục (công khai)
Route::get('/categories', [CategoryController::class, 'index']);

// gian hàng công khai
Route::get('/shops/{slug}', [ShopController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    // mở gian hàng (chưa cần là seller); sửa gian hàng thì phải là seller
    Route::post('/shops', [ShopController::class, 'store']);
    Route::put('/seller/shop', [ShopController::class, 'update'])->middleware('seller');

    Route::post('/user/update/{id}',[MemberController::class, 'updateProfile']);
    Route::post('/user/product/add', [ProductController::class, 'addProduct']);
    Route::get('/user/my-product', [ProductController::class, 'myProduct']);

    Route::get('/user/product/delete/{id}', [ProductController::class, 'deleteProduct']);
    Route::get('/user/product/{id}', [ProductController::class, 'getProduct']);

    Route::post('/user/product/update/{id}', [ProductController::class, 'updateProduct']);
    Route::post('/blog/comment/{id}', [BlogController::class,'storeComment']);
});

Route::get('/product', [ProductController::class, 'product']);
Route::get('/product/detail/{id}', [ProductController::class, 'detail']);
Route::post('/product/cart',[ProductController::class, 'productCart']);
Route::post('/checkout', [CheckoutController::class,'checkout'])
    ->middleware('auth:sanctum');



Route::get('/blog', [BlogController::class,'index']);
Route::get('/blog/detail/{id}', [BlogController::class,'detail']);

Route::get('/blog/comment/{id}', [BlogController::class,'getComment']);
Route::get('/blog/rate/{id}', [BlogController::class,'getRate']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/blog/rate/{id}', [BlogController::class,'storeRate']);

});

?>