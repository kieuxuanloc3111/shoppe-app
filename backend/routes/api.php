<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;

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
// VNPay trả kết quả (công khai — VNPay gọi, buyer có thể chưa có token)
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn']);
Route::get('/payment/vnpay/ipn', [PaymentController::class, 'vnpayIpn']);

// cây danh mục (công khai)
Route::get('/categories', [CategoryController::class, 'index']);

// gian hàng công khai
Route::get('/shops/{slug}', [ShopController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    // mở gian hàng (chưa cần là seller); sửa gian hàng thì phải là seller
    Route::post('/shops', [ShopController::class, 'store']);
    Route::put('/seller/shop', [ShopController::class, 'update'])->middleware('seller');

    Route::post('/user/update/{id}',[MemberController::class, 'updateProfile']);

    // quản lý sản phẩm — chỉ người bán (có shop active)
    Route::middleware('seller')->group(function () {
        Route::post('/user/product/add', [ProductController::class, 'addProduct']);
        Route::get('/user/my-product', [ProductController::class, 'myProduct']);
        Route::get('/user/product/delete/{id}', [ProductController::class, 'deleteProduct']);
        Route::get('/user/product/{id}', [ProductController::class, 'getProduct']);
        Route::post('/user/product/update/{id}', [ProductController::class, 'updateProduct']);

        // seller xem + xử lý đơn của shop mình
        Route::get('/seller/orders', [OrderController::class, 'sellerOrders']);
        Route::post('/seller/orders/{shopOrder}/confirm', [OrderController::class, 'confirm']);
        Route::post('/seller/orders/{shopOrder}/ship', [OrderController::class, 'ship']);
        Route::post('/seller/orders/{shopOrder}/deliver', [OrderController::class, 'deliver']);
    });

    // giỏ hàng server (buyer) — giá lấy từ DB, chặn vượt kho
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'add']);
    Route::put('/cart/{item}', [CartController::class, 'update']);
    Route::delete('/cart/{item}', [CartController::class, 'remove']);

    // buyer xem/thao tác đơn của mình
    Route::get('/orders', [OrderController::class, 'myOrders']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{shopOrder}/received', [OrderController::class, 'received']);
    Route::post('/orders/{shopOrder}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{order}/pay', [PaymentController::class, 'pay']); // VNPay: lấy URL redirect

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