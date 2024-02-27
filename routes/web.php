<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SerieController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
    });
    Route::resource('/customers', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/customers/{customer}/handle-ban', [CustomerController::class, 'handleBan'])->name('customers.handleBan');

    Route::resource('/series', SerieController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/series/{serie}/change-status', [SerieController::class, 'changeStatus'])->name('series.change-status');

    Route::resource('orders', OrderController::class)->only(['index', 'store', 'update', 'destroy']);

    // profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
