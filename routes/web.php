<?php
use App\Http\Controllers\{HomeController, CatalogController, CartController};
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{BannerController, BrandController, CategoryController, ConcernController, DashboardController, ProductController, ReviewController};
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard');
})->middleware('auth')->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/collections/{category}', [CatalogController::class, 'category'])->name('category');
Route::get('/brands/{brand}',         [CatalogController::class, 'brand'])->name('brand');
Route::get('/products/{product}',     [CatalogController::class, 'show'])->name('product');
Route::get('/search',                 [CatalogController::class, 'search'])->name('search');

Route::get('/cart',            [CartController::class, 'index'])->name('cart');
Route::post('/cart/add',       [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{key}',    [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{key}',   [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout',        [CartController::class, 'checkout'])->name('checkout');
Route::post('/checkout',       [CartController::class, 'placeOrder'])->name('checkout.place');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', DashboardController::class)->name('admin.dashboard');
    Route::resource('products', ProductController::class)->except('show')->names('admin.products');
    Route::resource('categories', CategoryController::class)->except('show')->names('admin.categories');
    Route::resource('brands', BrandController::class)->except('show')->names('admin.brands');
    Route::resource('banners', BannerController::class)->except('show')->names('admin.banners');
    Route::resource('concerns', ConcernController::class)->except('show')->names('admin.concerns');
    Route::resource('reviews', ReviewController::class)->except('show')->names('admin.reviews');
});

require __DIR__.'/auth.php';