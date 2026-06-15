<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $today = now()->toDateString();

        return response()->json([
            'cuci_hari_ini' => Booking::today()->where('status', 'selesai')->count(),
            'antrian_aktif' => Booking::today()->active()->count(),
            'pendapatan_hari_ini' => Payment::whereDate('paid_at', $today)
                ->where('status', 'success')->sum('amount'),
            'subscriber_aktif' => UserSubscription::where('status', 'active')
                ->where('expires_at', '>=', now())->count(),
            'rating_rata_rata' => round(Review::avg('rating'), 1),
            'total_pelanggan' => DB::table('users')->where('role', 'customer')->count(),
        ]);
    }

    public function revenue(Request $request)
    {
        $period = $request->get('period', 'weekly');

        if ($period === 'weekly') {
            $data = Payment::where('status', 'success')
                ->where('paid_at', '>=', now()->subDays(7))
                ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            $data = Payment::where('status', 'success')
                ->where('paid_at', '>=', now()->subDays(30))
                ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        return response()->json($data);
    }

    public function topServices()
    {
        $data = Booking::where('status', 'selesai')
            ->select('service_id', DB::raw('COUNT(*) as total'))
            ->groupBy('service_id')
            ->with('service:id,name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return response()->json($data);
    }
}
