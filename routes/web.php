<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\QueueController;
use App\Http\Controllers\Web\QueueHistoryController; // <- tambahkan ini
use App\Http\Controllers\Web\SubscriptionController;
use App\Http\Controllers\Web\PaymentController;

// Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated User
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Booking
    Route::get('booking', [BookingController::class, 'index'])->name('booking.index');
    Route::get('booking/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::post('booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    // Payment untuk booking
    Route::get('booking/{booking}/payment', [PaymentController::class, 'select'])->name('booking.payment');
    Route::post('booking/{booking}/payment', [PaymentController::class, 'process'])->name('booking.payment.process');

    // Queue
    Route::get('queue', [QueueController::class, 'live'])->name('queue.live');

    // Subscription
    Route::get('subscription', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::post('subscription/purchase', [SubscriptionController::class, 'purchase'])->name('subscription.purchase');

    // Payment History & Topup
    Route::get('payment/history', [PaymentController::class, 'history'])->name('payment.history');
    Route::post('payment/topup', [PaymentController::class, 'topup'])->name('payment.topup');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('revenue', [DashboardController::class, 'revenue'])->name('revenue');
    Route::get('queue/manage', [QueueController::class, 'manage'])->name('queue.manage');
    Route::get('queue/history', [QueueHistoryController::class, 'index'])->name('queue.history');
    Route::put('booking/{booking}/status', [BookingController::class, 'updateStatus'])->name('booking.status');
});
