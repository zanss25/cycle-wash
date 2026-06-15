<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notification;

class NotificationService
{
    protected WhatsAppService $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function sendStatusUpdate(Booking $booking): void
    {
        $user = $booking->user;

        $message = match ($booking->status) {
            'dicuci' => "🧽 Motor Anda ({$booking->plate_number}) sedang dalam proses pencucian. Mohon tunggu notifikasi selanjutnya.",
            'selesai' => "✅ Motor Anda ({$booking->plate_number}) sudah selesai dicuci! Silakan ambil di lokasi.",
            default => null,
        };

        if (!$message) return;

        // Send WhatsApp
        $this->whatsapp->send($user->phone, $message);

        // Send Push Notification
        $this->sendPushNotification($user, 'Smart-Wash', $message);

        // Save to database
        Notification::create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'type' => 'whatsapp',
            'title' => 'Status Update - ' . ucfirst($booking->status),
            'message' => $message,
            'sent_at' => now(),
        ]);
    }

    public function sendSubscriptionReminder($user, $subscription): void
    {
        $message = "⚠️ Kuota langganan Anda tersisa {$subscription->remaining_quota}x cuci. Perpanjang sekarang agar tetap hemat!";

        $this->whatsapp->send($user->phone, $message);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'whatsapp',
            'title' => 'Reminder Langganan',
            'message' => $message,
            'sent_at' => now(),
        ]);
    }

    protected function sendPushNotification($user, string $title, string $body): void
    {
        if (!$user->fcm_token) return;

        // Firebase Cloud Messaging
        \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'key=' . config('services.fcm.server_key'),
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $user->fcm_token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
        ]);
    }
}
