<?php

namespace App\Http\Controllers\Web; // <-- perbaiki namespace

use App\Models\Booking;
use App\Models\Service;
use App\Services\NotificationService;
use App\Events\BookingStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = auth()->user()->bookings()
            ->with('service')
            ->when($request->date, fn($q) => $q->where('booking_date', $request->date))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('booking_date')
            ->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($bookings);
        }

        return view('booking.index', compact('bookings'));
    }

    public function create()
{
    $services = Service::all();
    return view('booking.create', compact('services'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'plate_number' => 'required|string|max:20',
            'motorcycle_type' => 'required|in:matic,sport,bebek,trail',
            'booking_date' => 'required|date|after_or_equal:today',
            'arrival_time' => 'required|date_format:H:i',
            'payment_method' => 'required|in:cash,qris,ewallet,wallet,subscription',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        // Generate queue number
        $queueNumber = Booking::where('booking_date', $validated['booking_date'])
            ->max('queue_number') + 1;

        // Handle subscription payment
        if ($validated['payment_method'] === 'subscription') {
            $subscription = auth()->user()->activeSubscription;
            if (!$subscription || !$subscription->hasQuota()) {
                return back()->withInput()->withErrors(['payment_method' => 'Tidak ada langganan aktif atau kuota habis']);
            }
            $subscription->decrement('remaining_quota');
        }

        // Handle wallet payment
        if ($validated['payment_method'] === 'wallet') {
            if (auth()->user()->wallet_balance < $service->price) {
                return back()->withInput()->withErrors(['payment_method' => 'Saldo wallet tidak mencukupi']);
            }
            auth()->user()->decrement('wallet_balance', $service->price);
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'service_id' => $validated['service_id'],
            'queue_number' => $queueNumber,
            'plate_number' => strtoupper($validated['plate_number']),
            'motorcycle_type' => $validated['motorcycle_type'],
            'booking_date' => $validated['booking_date'],
            'arrival_time' => $validated['arrival_time'],
            'status' => 'antrian',
            'payment_method' => $validated['payment_method'],
            'payment_status' => in_array($validated['payment_method'], ['wallet', 'subscription']) ? 'paid' : ($validated['payment_method'] === 'cash' ? 'pending' : 'unpaid'),
            'total_amount' => $service->price,
            'qr_code' => Str::uuid()->toString(),
        ]);

        // Update hourly traffic
        DB::table('hourly_traffic')->updateOrInsert(
            ['date' => $validated['booking_date'], 'hour' => (int) substr($validated['arrival_time'], 0, 2)],
            ['total_bookings' => DB::raw('total_bookings + 1')]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Booking berhasil!',
                'booking' => $booking->load('service'),
                'queue_number' => $queueNumber,
            ], 201);
        }

        // Jika perlu bayar online (qris/ewallet), redirect ke payment
        if (in_array($validated['payment_method'], ['qris', 'ewallet'])) {
            return redirect()->route('booking.payment', $booking)
                ->with('success', 'Booking berhasil! Silakan selesaikan pembayaran.');
        }

        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil! Nomor antrian Anda: ' . $queueNumber);
    }

    public function show(Booking $booking)
    {
        // Pastikan user hanya bisa lihat booking sendiri
        if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $booking->load(['user', 'service']);

        if (request()->wantsJson()) {
            return response()->json($booking);
        }

        return view('booking.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        if ($booking->status !== 'antrian') {
            return back()->withErrors(['error' => 'Hanya booking dengan status antrian yang bisa dibatalkan']);
        }

        $booking->update(['status' => 'dibatalkan']);

        // Refund jika sudah bayar via wallet
        if ($booking->payment_status === 'paid' && $booking->payment_method === 'wallet') {
            auth()->user()->increment('wallet_balance', $booking->total_amount);
        }

        // Refund subscription quota
        if ($booking->payment_method === 'subscription') {
            $subscription = auth()->user()->activeSubscription;
            if ($subscription) {
                $subscription->increment('remaining_quota');
            }
        }

        return back()->with('success', 'Booking berhasil dibatalkan');
    }

    // Admin only
    public function updateStatus(Booking $booking, Request $request)
    {
        $request->validate([
            'status' => 'required|in:antrian,dicuci,selesai,dibatalkan',
        ]);

        $booking->update([
            'status' => $request->status,
            'started_at' => $request->status === 'dicuci' ? now() : $booking->started_at,
            'completed_at' => $request->status === 'selesai' ? now() : null,
        ]);

        app(NotificationService::class)->sendStatusUpdate($booking);
        event(new BookingStatusChanged($booking));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Status berhasil diperbarui',
                'booking' => $booking->fresh(),
            ]);
        }

        return back()->with('success', 'Status berhasil diperbarui ke: ' . $request->status);
    }
}
