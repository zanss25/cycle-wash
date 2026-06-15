<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['user:id,name', 'booking.service:id,name'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($booking->status !== 'selesai') {
            return response()->json(['error' => 'Hanya bisa review booking yang sudah selesai'], 422);
        }

        if ($booking->review) {
            return response()->json(['error' => 'Anda sudah memberikan review'], 422);
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'booking_id' => $request->booking_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review berhasil ditambahkan!',
            'review' => $review,
        ], 201);
    }

    public function averageRating()
    {
        $avg = Review::avg('rating');
        $total = Review::count();

        return response()->json([
            'average_rating' => round($avg, 1),
            'total_reviews' => $total,
        ]);
    }
}
