<?php

use App\Http\Controllers\AdminCatalogController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroqController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminLandingPageController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GeminiController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/kategori', [CategoryController::class, 'index']);
    Route::get('/katalog', [CatalogController::class, 'index']);
    Route::get('/katalog/{catalog}', [CatalogController::class, 'show'])->name('catalog.show');
    Route::get('/profil', [ProfileController::class, 'edit']);
    Route::post('/profil', [ProfileController::class, 'update']);

    // Endpoint AI recommendation (Gemini)
    Route::post('/ai/recommend', [GroqController::class, 'recommend'])->middleware('throttle:ai-recommend');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('activity/feed', [AdminDashboardController::class, 'activityFeed'])->name('activity.feed');
        Route::get('profile', [ProfileController::class, 'adminEdit'])->name('profile.edit');
        Route::post('profile', [ProfileController::class, 'adminUpdate'])->name('profile.update');
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::resource('catalogs', AdminCatalogController::class)->except(['show']);

        Route::get('landing-page', [AdminLandingPageController::class, 'edit'])->name('landing.edit');
        Route::post('landing-page', [AdminLandingPageController::class, 'update'])->name('landing.update');
    });
});
