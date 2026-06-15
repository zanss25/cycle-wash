<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Events\BookingStatusChanged;

class BookingService
{
    public function createBooking(array $data): Booking
    {
        $service = Service::findOrFail($data['service_id']);

        $booking = Booking::create([
            'user_id' => $data['user_id'],
            'service_id' => $service->id,
            'booking_date' => $data['booking_date'],
            'booking_time' => $data['booking_time'],
            'vehicle_type' => $data['vehicle_type'],
            'plate_number' => $data['plate_number'],
            'status' => 'pending',
            'total_price' => $service->price,
        ]);

        event(new BookingStatusChanged($booking));
        return $booking;
    }

    public function updateStatus(Booking $booking, string $status): Booking
    {
        $booking->update(['status' => $status]);
        event(new BookingStatusChanged($booking));
        return $booking;
    }

    public function cancelBooking(Booking $booking): Booking
    {
        if (in_array($booking->status, ['pending', 'confirmed'])) {
            $booking->update(['status' => 'cancelled']);
            event(new BookingStatusChanged($booking));
        }
        return $booking;
    }
}
