<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)->latest()->take(5)->get();
        $activeBooking = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->first();

        return view('dashboard.index', compact('bookings', 'activeBooking'));
    }

    public function admin()
    {
        $todayBookings = Booking::whereDate('booking_date', today())->count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $pendingQueue = Booking::whereDate('booking_date', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        return view('dashboard.revenue', compact('todayBookings', 'totalRevenue', 'pendingQueue'));
    }

    public function revenue()
    {
        $payments = Payment::where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        return view('dashboard.revenue', compact('payments'));
    }
}
