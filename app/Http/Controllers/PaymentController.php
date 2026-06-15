<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function select(Booking $booking)
    {
        // Pastikan booking milik user ini
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.select', compact('booking'));
    }

    public function process(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qris,ewallet,transfer',
        ]);

        $booking->update([
            'payment_method' => $request->payment_method,
            'payment_status' => 'paid',
            'status' => 'antrian',
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Pembayaran berhasil! Nomor antrian Anda: ' . $booking->queue_number);
    }
}
