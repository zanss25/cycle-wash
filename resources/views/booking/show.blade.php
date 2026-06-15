@extends('layouts.app')
@section('title', 'Detail Booking')
@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6">Detail Booking #{{ $booking->id }}</h1>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="grid grid-cols-2 gap-4">
            <div><span class="text-gray-500">Layanan:</span><p class="font-bold">{{ $booking->service->name ?? '-' }}</p></div>
            <div><span class="text-gray-500">Harga:</span><p class="font-bold">Rp {{ number_format($booking->total_price) }}</p></div>
            <div><span class="text-gray-500">Tanggal:</span><p class="font-bold">{{ $booking->booking_date }}</p></div>
            <div><span class="text-gray-500">Jam:</span><p class="font-bold">{{ $booking->booking_time }}</p></div>
            <div><span class="text-gray-500">Kendaraan:</span><p class="font-bold">{{ $booking->vehicle_type }}</p></div>
            <div><span class="text-gray-500">Plat:</span><p class="font-bold">{{ $booking->plate_number }}</p></div>
            <div class="col-span-2">
                <span class="text-gray-500">Status:</span>
                <p class="mt-1">
                    <span class="px-3 py-1 rounded text-sm
                        @if($booking->status == 'completed') bg-green-100 text-green-700
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                        @elseif($booking->status == 'in_progress') bg-blue-100 text-blue-700
                        @else bg-yellow-100 text-yellow-700
                        @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </p>
            </div>
        </div>

        @if(in_array($booking->status, ['pending', 'confirmed']))
        <form action="{{ route('booking.cancel', $booking) }}" method="POST" class="mt-6">
            @csrf
            <button type="submit" class="w-full bg-red-500 text-white py-2 rounded hover:bg-red-600"
                onclick="return confirm('Yakin ingin membatalkan?')">
                Batalkan Booking
            </button>
        </form>
        @endif
    </div>

    <a href="{{ route('booking.index') }}" class="inline-block mt-4 text-blue-600 hover:underline">← Kembali</a>
</div>
@endsection
