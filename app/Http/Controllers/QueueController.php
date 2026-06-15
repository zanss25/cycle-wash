<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    public function liveQueue()
    {
        $today = now()->toDateString();

        $totalAntrian = Booking::today()->where('status', 'antrian')->count();
        $sedangDicuci = Booking::today()->where('status', 'dicuci')->count();
        $selesaiHariIni = Booking::today()->where('status', 'selesai')->count();

        // Estimasi waktu tunggu
        $pendingBookings = Booking::today()
            ->where('status', 'antrian')
            ->with('service')
            ->get();

        $estimasiMenit = $pendingBookings->sum(fn($b) => $b->service->duration_minutes);

        // Daftar antrean aktif
        $queueList = Booking::today()
            ->whereIn('status', ['antrian', 'dicuci'])
            ->with(['user:id,name', 'service:id,name,duration_minutes'])
            ->orderBy('queue_number')
            ->get();

        return response()->json([
            'total_antrian' => $totalAntrian,
            'sedang_dicuci' => $sedangDicuci,
            'selesai_hari_ini' => $selesaiHariIni,
            'estimasi_menit' => $estimasiMenit,
            'queue_list' => $queueList,
        ]);
    }

    public function densityChart(Request $request)
    {
        $days = $request->get('days', 30);

        $data = DB::table('hourly_traffic')
            ->where('date', '>=', now()->subDays($days))
            ->select('hour', DB::raw('ROUND(AVG(total_bookings)) as avg_bookings'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return response()->json($data);
    }

    public function myPosition()
    {
        $today = now()->toDateString();

        $myBooking = Booking::where('user_id', auth()->id())
            ->where('booking_date', $today)
            ->whereIn('status', ['antrian', 'dicuci'])
            ->first();

        if (!$myBooking) {
            return response()->json(['message' => 'Tidak ada antrean aktif'], 404);
        }

        $posisi = Booking::where('booking_date', $today)
            ->where('status', 'antrian')
            ->where('queue_number', '<', $myBooking->queue_number)
            ->count() + 1;

        return response()->json([
            'booking' => $myBooking->load('service'),
            'posisi' => $posisi,
            'estimasi_menit' => $posisi * ($myBooking->service->duration_minutes ?? 15),
        ]);
    }
}
