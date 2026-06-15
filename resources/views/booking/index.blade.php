@extends('layouts.app')
@section('title', 'Daftar Booking')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Daftar Booking Saya</h1>
    <a href="{{ route('booking.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Booking Baru</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3">No</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Layanan</th>
                <th>Kendaraan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $i => $booking)
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3">{{ $bookings->firstItem() + $i }}</td>
                <td>{{ $booking->booking_date }}</td>
                <td>{{ $booking->booking_time }}</td>
                <td>{{ $booking->service->name ?? '-' }}</td>
                <td>{{ $booking->plate_number }} ({{ $booking->vehicle_type }})</td>
                <td>
                    <span class="px-2 py-1 rounded text-xs
                        @if($booking->status == 'completed') bg-green-100 text-green-700
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                        @elseif($booking->status == 'in_progress') bg-blue-100 text-blue-700
                        @else bg-yellow-100 text-yellow-700
                        @endif">
                        {{ $booking->status }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('booking.show', $booking) }}" class="text-blue-600 hover:underline">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Belum ada booking</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3">{{ $bookings->links() }}</div>
</div>
@endsection
