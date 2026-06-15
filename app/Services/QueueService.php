<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class QueueService
{
    public function getEstimatedWaitTime(int $queueNumber, string $date): int
    {
        $bookingsAhead = Booking::where('booking_date', $date)
            ->where('queue_number', '<', $queueNumber)
            ->where('status', 'antrian')
            ->with('service')
            ->get();

        return $bookingsAhead->sum(fn($b) => $b->service->duration_minutes);
    }

    public function getTodayQueue()
    {
    $bookings = Booking::whereDate('created_at', Carbon::today())
        ->orderBy('queue_number', 'asc')
        ->get();

    $inQueue = $bookings->where('status', 'antrian')->count();
    $estimatedTime = $bookings->where('status', 'antrian')->sum(function ($b) {
        return $b->service->duration_minutes ?? 30;
    });

    return [
        'total_in_queue' => $inQueue,
        'estimated_clear_time' => $estimatedTime,
        'bookings' => $bookings,
    ];
    }

    public function getNextInQueue(): ?Booking
    {
        return Booking::today()
            ->where('status', 'antrian')
            ->orderBy('queue_number')
            ->first();
    }

    public function getTodayStats(): array
    {
        return [
            'total' => Booking::today()->count(),
            'antrian' => Booking::today()->where('status', 'antrian')->count(),
            'dicuci' => Booking::today()->where('status', 'dicuci')->count(),
            'selesai' => Booking::today()->where('status', 'selesai')->count(),
        ];
    }
}
