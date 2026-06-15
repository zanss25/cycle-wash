<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QRCodeController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Check if QR is an existing booking
        $booking = Booking::where('qr_code', $request->qr_code)->first();

        if ($booking) {
            return response()->json([
                'type' => 'existing_booking',
                'booking' => $booking->load(['user', 'service']),
            ]);
        }

        // QR code for instant queue registration
        $defaultService = Service::where('is_active', true)->first();
        $queueNumber = Booking::where('booking_date', now()->toDateString())
            ->max('queue_number') + 1;

        $newBooking = Booking::create([
            'user_id' => auth()->id(),
            'service_id' => $request->get('service_id', $defaultService->id),
            'queue_number' => $queueNumber,
            'plate_number' => $request->get('plate_number', 'SCAN-' . strtoupper(Str::random(5))),
            'motorcycle_type' => $request->get('motorcycle_type', 'matic'),
            'booking_date' => now()->toDateString(),
            'arrival_time' => now()->format('H:i'),
            'status' => 'antrian',
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'total_amount' => $defaultService->price,
            'qr_code' => Str::uuid()->toString(),
        ]);

        return response()->json([
            'type' => 'new_queue',
            'message' => 'Berhasil masuk antrean!',
            'booking' => $newBooking->load('service'),
            'queue_number' => $queueNumber,
        ], 201);
    }

    public function generate(Booking $booking)
    {
        return response()->json([
            'qr_data' => $booking->qr_code,
            'booking_id' => $booking->id,
            'queue_number' => $booking->queue_number,
        ]);
    }
}
