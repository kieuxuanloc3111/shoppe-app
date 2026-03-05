<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FRONTEND HOME (PUBLIC)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Frontend\HomeController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');


/*
|--------------------------------------------------------------------------
| AUTH DEFAULT (Laravel)
|--------------------------------------------------------------------------
*/
Auth::routes();


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (LEVEL = 1)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/profile', [UserController::class, 'profile'])
            ->name('profile');

        Route::post('/profile', [UserController::class, 'update'])
            ->name('profile.update');

        // USER LIST
        Route::get('/user', [UserController::class, 'index'])
            ->name('user.index');

        Route::get('/user/{id}/edit', [UserController::class, 'edit'])
            ->name('user.edit');

        Route::put('/user/{id}', [UserController::class, 'update_member'])
            ->name('user.update');

        Route::get('/user/{id}/delete', [UserController::class, 'destroy'])
            ->name('user.delete');
        // PRODUCT ADMIN
        Route::get('/product', [AdminProductController::class, 'index'])
            ->name('product.index');

        Route::get('/product/{id}/edit', [AdminProductController::class, 'edit'])
            ->name('product.edit');

        Route::put('/product/{id}', [AdminProductController::class, 'update'])
            ->name('product.update');

        Route::get('/product/{id}/delete', [AdminProductController::class, 'destroy'])
            ->name('product.delete');

        
        Route::get('/history', [HistoryController::class, 'index'])
            ->name('history.index');

        // COUNTRY
        Route::get('/country', [CountryController::class, 'index'])->name('country.index');
        Route::get('/country/create', [CountryController::class, 'create'])->name('country.create');
        Route::post('/country', [CountryController::class, 'store'])->name('country.store');
        Route::get('/country/{id}/edit', [CountryController::class, 'edit'])->name('country.edit');
        Route::put('/country/{id}', [CountryController::class, 'update'])->name('country.update');
        Route::delete('/country/{id}', [CountryController::class, 'destroy'])->name('country.destroy');

        // BLOG
        Route::get('/blogs', [AdminBlogController::class, 'index'])->name('blog.index');
        Route::get('/blogs/create', [AdminBlogController::class, 'create'])->name('blog.create');
        Route::post('/blogs', [AdminBlogController::class, 'store'])->name('blog.store');
        Route::get('/blogs/{id}/edit', [AdminBlogController::class, 'edit'])->name('blog.edit');
        Route::put('/blogs/{id}', [AdminBlogController::class, 'update'])->name('blog.update');
        Route::get('/blogs/{id}/delete', [AdminBlogController::class, 'destroy'])->name('blog.delete');

        // CATEGORY
        Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
        Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
        Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
        Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
        Route::put('/category/{id}', [CategoryController::class, 'update'])->name('category.update');
        Route::get('/category/{id}/delete', [CategoryController::class, 'destroy'])->name('category.delete');

        // BRAND
        Route::get('/brand', [BrandController::class, 'index'])->name('brand.index');
        Route::get('/brand/create', [BrandController::class, 'create'])->name('brand.create');
        Route::post('/brand', [BrandController::class, 'store'])->name('brand.store');
        Route::get('/brand/{id}/edit', [BrandController::class, 'edit'])->name('brand.edit');
        Route::put('/brand/{id}', [BrandController::class, 'update'])->name('brand.update');
        Route::get('/brand/{id}/delete', [BrandController::class, 'destroy'])->name('brand.delete');
    });


/*
|--------------------------------------------------------------------------
| MEMBER AUTH (LOGIN / REGISTER)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Frontend\AuthController;

Route::prefix('member')->group(function () {

    Route::get('/register', [AuthController::class, 'registerForm'])
        ->name('member.register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('member.register.post');

    Route::get('/login', [AuthController::class, 'loginForm'])
        ->name('member.login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('member.login.post');
});


/*
|--------------------------------------------------------------------------
| MEMBER ROUTES (LEVEL = 0)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CommentController;
Route::prefix('member')
    ->middleware(['auth', 'member'])
    ->group(function () {
        Route::post('/blog/rate', [FrontendBlogController::class, 'rate'])
            ->name('blog.rate');

        Route::post('/blog/comment', [CommentController::class, 'store'])
            ->name('blog.comment');
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('member.logout');


        Route::get('/profile', [AuthController::class, 'profileForm'])
            ->name('member.profile');

        Route::post('/profile', [AuthController::class, 'updateProfile'])
            ->name('member.profile.update');

        // PRODUCT
        Route::get('/add-product', [ProductController::class, 'create'])
            ->name('member.product.add');

        Route::post('/add-product', [ProductController::class, 'store'])
            ->name('member.product.store');

        Route::get('/my-product', [ProductController::class, 'myProduct'])
            ->name('member.product.my');

        Route::get('/product/{id}/edit', [ProductController::class, 'edit'])
            ->name('member.product.edit');

        Route::post('/product/{id}/update', [ProductController::class, 'update'])
            ->name('member.product.update');

        Route::get('/product/{id}/delete', [ProductController::class, 'destroy'])
            ->name('member.product.delete');



        Route::post('/add-to-cart', [CartController::class, 'add'])
            ->name('member.cart.add');
    });
/*
|--------------------------------------------------------------------------
| home
|--------------------------------------------------------------------------
*/
Route::get('/home', [HomeController::class, 'index'])
    ->name('home');

Route::get('product/{id}', [ProductController::class, 'detail'])
    ->name('product.detail');
/*
|--------------------------------------------------------------------------
| cart
|--------------------------------------------------------------------------
*/
Route::post('/add-to-cart', [CartController::class, 'add'])
    ->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/cart/update', [CartController::class, 'update'])
    ->name('cart.update');

Route::post('/cart/delete', [CartController::class, 'delete'])
    ->name('cart.delete');

/*
|--------------------------------------------------------------------------
| BLOG FRONTEND
|--------------------------------------------------------------------------
*/

Route::get('/blog', [FrontendBlogController::class, 'index'])
    ->name('blog.index');

Route::get('/blog/{id}', [FrontendBlogController::class, 'detail'])
    ->name('blog.detail');

use App\Mail\MailNotify;
use Illuminate\Support\Facades\Mail;

Route::get('/test-mail', function () {
    Mail::to('kieuxuanloc3111@gmail.com')->send(new MailNotify());
    return 'Send mail OK';
});


/*
|--------------------------------------------------------------------------
| checkout
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ForgotPasswordController;

Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index');

Route::post('/checkout', [CheckoutController::class, 'process'])
    ->name('checkout.process');

    // search

Route::get('/search', [ProductController::class, 'search'])
    ->name('search');

Route::get('/advancedsearch', [ProductController::class, 'advancedSearch'])
    ->name('advancedsearch');
Route::get('/filter-price', [ProductController::class, 'filterPrice'])
    ->name('filter.price');

// forget pas
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendLink'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'resetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ForgotPasswordController::class, 'updatePassword'])
    ->name('password.update');