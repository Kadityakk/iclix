<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileSelectionController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->middleware('guest')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');

    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/checkout/{plan}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{plan}', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::patch('/account', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');

    Route::post('/profiles/{profile}/select', ProfileSelectionController::class)->name('profiles.select');
    Route::resource('profiles', ProfileController::class)->except('show');

    Route::middleware(['subscribed', 'profile.selected'])->group(function () {
        Route::get('/browse', BrowseController::class)->name('browse');

        Route::get('/movies', [CatalogController::class, 'movies'])->name('movies.index');
        Route::get('/series', [CatalogController::class, 'series'])->name('series.index');
        Route::get('/trending', [CatalogController::class, 'trending'])->name('trending.index');
        Route::get('/genres', [CatalogController::class, 'genres'])->name('genres.index');

        Route::get('/title/{content:slug}', [ContentController::class, 'show'])->name('contents.show');
        Route::get('/search', SearchController::class)->middleware('throttle:120,1')->name('search');

        Route::get('/my-list', [WatchlistController::class, 'index'])->name('my-list.index');
        Route::post('/my-list/{content}', [WatchlistController::class, 'store'])->name('my-list.store');
        Route::delete('/my-list/{content}', [WatchlistController::class, 'destroy'])->name('my-list.destroy');

        Route::post('/ratings/{content}', [RatingController::class, 'store'])->name('ratings.store');

        Route::get('/watch/{content:slug}/{episode?}', [PlayerController::class, 'show'])->name('watch');
        Route::get('/stream/{content}/{episode?}', StreamController::class)->name('stream');
        Route::post('/progress', [ProgressController::class, 'store'])->name('progress.store');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::patch('contents/{content}/featured', [Admin\ContentController::class, 'toggleFeatured'])->name('contents.featured');
    Route::resource('contents', Admin\ContentController::class)->except('show');
    Route::resource('contents.seasons', Admin\SeasonController::class)->except('show')->shallow();
    Route::resource('seasons.episodes', Admin\EpisodeController::class)->except('show')->shallow();

    Route::resource('genres', Admin\GenreController::class)->except('show');
    Route::resource('plans', Admin\PlanController::class)->except('show');
    Route::resource('users', Admin\UserController::class)->only(['index', 'show', 'update']);
});

require __DIR__.'/auth.php';
