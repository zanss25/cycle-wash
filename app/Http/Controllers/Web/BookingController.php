<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('service')
            ->latest()
            ->paginate(10);

        return view('booking.index', compact('bookings'));
    }

    public function create()
    {
        $services = Service::active()->get();
        return view('booking.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'vehicle_type' => 'required|in:matic,sport,bebek',
            'plate_number' => 'required|string',
        ]);

        $booking = $this->bookingService->createBooking([
            'user_id' => Auth::id(),
            'service_id' => $request->service_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'vehicle_type' => $request->vehicle_type,
            'plate_number' => $request->plate_number,
        ]);

        return redirect()->route('booking.show', $booking)->with('success', 'Booking berhasil!');
    }

    public function show(Booking $booking)
    {
        $booking->load('service', 'user');
        return view('booking.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        $this->bookingService->cancelBooking($booking);
        return back()->with('success', 'Booking dibatalkan.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate(['status' => 'required|in:confirmed,in_progress,completed,cancelled']);
        $this->bookingService->updateStatus($booking, $request->status);
        return back()->with('success', 'Status diupdate.');
    }
}
