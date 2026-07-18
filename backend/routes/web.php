<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROOT — Laravel app is admin-only now (React SPA is the client on :3000)
| Guest -> admin login, logged-in admin -> dashboard
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check() && auth()->user()->level == 1) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| AUTH (admin login) — reset unified to React /forgot-password, so off here
|--------------------------------------------------------------------------
*/
Auth::routes(['register' => false, 'reset' => false, 'verify' => false, 'confirm' => false]);


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
