@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="text-center py-20">
    <h1 class="text-5xl font-bold text-blue-600 mb-4">Smart Wash</h1>
    <p class="text-xl text-gray-600 mb-8">Cuci Motor Cerdas, Cepat & Terpercaya</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow">
            <i class="fas fa-clock text-3xl text-blue-500 mb-3"></i>
            <h3 class="font-bold text-lg">Antrian Real-Time</h3>
            <p class="text-gray-500 mt-2">Pantau antrian langsung tanpa perlu menunggu di tempat</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <i class="fas fa-calendar-check text-3xl text-green-500 mb-3"></i>
            <h3 class="font-bold text-lg">Booking Online</h3>
            <p class="text-gray-500 mt-2">Pesan jadwal cuci motor kapan saja dari HP kamu</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <i class="fas fa-crown text-3xl text-yellow-500 mb-3"></i>
            <h3 class="font-bold text-lg">Paket Langganan</h3>
            <p class="text-gray-500 mt-2">Hemat lebih banyak dengan paket cuci bulanan</p>
        </div>
    </div>

    <a href="{{ route('register') }}" class="inline-block mt-10 bg-blue-600 text-white px-8 py-3 rounded-lg text-lg hover:bg-blue-700">
        Mulai Sekarang
    </a>
</div>
@endsection
