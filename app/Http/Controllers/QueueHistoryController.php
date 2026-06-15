<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;

class QueueHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Queue::where('user_id', auth()->id())
            ->whereIn('status', ['served', 'no_show', 'cancelled'])
            ->orderBy('created_at', 'desc');

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $histories = $query->paginate(20);

        // Summary stats
        $today = now()->toDateString();
        $totalServed = Queue::where('user_id', auth()->id())
            ->where('status', 'served')
            ->whereDate('created_at', $request->date ?? $today)
            ->count();

        $avgWait = Queue::where('user_id', auth()->id())
            ->where('status', 'served')
            ->whereDate('created_at', $request->date ?? $today)
            ->avg('wait_time');

        $noShows = Queue::where('user_id', auth()->id())
            ->where('status', 'no_show')
            ->whereDate('created_at', $request->date ?? $today)
            ->count();

        return view('queue.history', compact('histories', 'totalServed', 'avgWait', 'noShows'));
    }
}
