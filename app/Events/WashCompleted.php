<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WashCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->booking->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => "Motor Anda ({$this->booking->plate_number}) sudah selesai dicuci!",
            'booking_id' => $this->booking->id,
        ];
    }
}
