<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GroqController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarberShopController;
use App\Http\Controllers\LandingPageApiController;
use Illuminate\Support\Facades\Route;

// Public routes (tidak perlu login)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/register', [AuthController::class, 'apiRegister']);
});

Route::get('/landing-page', [LandingPageApiController::class, 'show']);
Route::get('/barber-shops', [BarberShopController::class, 'index']);
Route::get('/nearby-barbers', [BarberShopController::class, 'nearby']);

// Mobile-friendly public API (tanpa perlu login)
Route::get('/categories', [CategoryController::class, 'apiIndex']);
Route::get('/catalogs', [CatalogController::class, 'apiIndex']);
Route::get('/catalogs/{catalog}', [CatalogController::class, 'apiShow']);
Route::post('/ai/recommend', [GroqController::class, 'recommend']);

// Backward compatible Indonesian paths (public)
Route::get('/kategori', [CategoryController::class, 'apiIndex']);
Route::get('/katalog', [CatalogController::class, 'apiIndex']);
Route::get('/katalog/{catalog}', [CatalogController::class, 'apiShow']);

// Protected routes (butuh token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'apiLogout']);

    Route::get('/profile', [ProfileController::class, 'apiShow']);
    Route::put('/profile', [ProfileController::class, 'apiUpdate']);

    // --- Admin REST endpoints (mobile) ---
    Route::prefix('admin')->group(function () {
        Route::get('/categories', [CategoryController::class, 'apiAdminIndex']);
        Route::post('/categories', [CategoryController::class, 'apiAdminStore']);
        Route::put('/categories/{category}', [CategoryController::class, 'apiAdminUpdate']);
        Route::delete('/categories/{category}', [CategoryController::class, 'apiAdminDestroy']);

        Route::get('/catalogs', [CatalogController::class, 'apiAdminIndex']);
        Route::post('/catalogs', [CatalogController::class, 'apiAdminStore']);
        Route::put('/catalogs/{catalog}', [CatalogController::class, 'apiAdminUpdate']);
        Route::delete('/catalogs/{catalog}', [CatalogController::class, 'apiAdminDestroy']);

        Route::put('/landing-page', [LandingPageApiController::class, 'update']);
    });
});