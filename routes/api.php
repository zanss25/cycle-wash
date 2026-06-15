<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\AuthController;

// Public routes (tanpa auth)
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::get('services', function () {
    return \App\Models\Service::active()->get();
});

Route::get('queue/live', [QueueController::class, 'liveQueue']);
Route::get('queue/density', [QueueController::class, 'densityChart']);
Route::get('reviews', [ReviewController::class, 'index']);
Route::get('reviews/average', [ReviewController::class, 'averageRating']);
Route::get('subscriptions/plans', [SubscriptionController::class, 'plans']);

// Authenticated routes (customer & admin)
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // Booking
    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('bookings/my', [BookingController::class, 'myBookings']);
    Route::get('bookings/{booking}', [BookingController::class, 'show']);
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel']);

    // Queue
    Route::get('queue/my-position', [QueueController::class, 'myPosition']);

    // QR Code
    Route::post('qr/scan', [QRCodeController::class, 'scan']);
    Route::get('qr/{booking}', [QRCodeController::class, 'generate']);

    // Subscription
    Route::post('subscriptions/purchase', [SubscriptionController::class, 'purchase']);
    Route::get('subscriptions/my', [SubscriptionController::class, 'mySubscription']);
    Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew']);

    // Payment & Wallet
    Route::post('payments/topup', [PaymentController::class, 'topup']);
    Route::post('payments/pay', [PaymentController::class, 'pay']);
    Route::get('payments/history', [PaymentController::class, 'history']);
    Route::get('payments/balance', [PaymentController::class, 'balance']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'unreadCount']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Reviews
    Route::post('reviews', [ReviewController::class, 'store']);

    // Admin only routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('dashboard/revenue', [DashboardController::class, 'revenue']);
        Route::get('dashboard/top-services', [DashboardController::class, 'topServices']);
        Route::get('queue/manage', [QueueController::class, 'manage']);
        Route::put('bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    });
});
