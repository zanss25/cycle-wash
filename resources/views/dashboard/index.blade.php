@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name }} 👋</h1>
    <p class="text-gray-500 text-sm mt-1">Berikut ringkasan aktivitas motor wash hari ini</p>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-8">
    <div class="card-hover bg-white p-4 md:p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-motorcycle text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Hari Ini</p>
                <p class="text-xl font-bold text-gray-800">{{ $todayBookings ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="card-hover bg-white p-4 md:p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Selesai</p>
                <p class="text-xl font-bold text-gray-800">{{ $completedToday ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="card-hover bg-white p-4 md:p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Antrian</p>
                <p class="text-xl font-bold text-gray-800">{{ $inQueue ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="card-hover bg-white p-4 md:p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-wallet text-purple-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Pendapatan</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-6 rounded-xl text-white">
        <h3 class="text-lg font-semibold mb-2"><i class="fas fa-plus-circle mr-2"></i>Booking Baru</h3>
        <p class="text-blue-100 text-sm mb-4">Tambah pelanggan baru ke antrian hari ini</p>
        <a href="/booking/create" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg font-medium text-sm hover:bg-blue-50 transition">
            <i class="fas fa-plus mr-2"></i> Buat Booking
        </a>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-700 p-6 rounded-xl text-white">
        <h3 class="text-lg font-semibold mb-2"><i class="fas fa-tv mr-2"></i>Lihat Antrian</h3>
        <p class="text-green-100 text-sm mb-4">Pantau antrian secara real-time</p>
        <a href="/queue" class="inline-flex items-center px-4 py-2 bg-white text-green-600 rounded-lg font-medium text-sm hover:bg-green-50 transition">
            <i class="fas fa-eye mr-2"></i> Buka Antrian
        </a>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-4 md:p-6 border-b">
        <h2 class="text-lg font-semibold text-gray-800"><i class="fas fa-history mr-2 text-gray-400"></i>Aktivitas Terbaru</h2>
    </div>
    <div class="divide-y">
        @forelse($recentBookings ?? [] as $booking)
        <div class="p-4 flex items-center gap-4 hover:bg-gray-50 transition">
            <div class="w-10 h-10 rounded-full flex items-center justify-center
                {{ $booking->status === 'completed' ? 'bg-green-100' : 'bg-yellow-100' }}">
                <i class="fas {{ $booking->status === 'completed' ? 'fa-check text-green-600' : 'fa-spinner text-yellow-600' }}"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 truncate">{{ $booking->user->name ?? 'Customer' }}</p>
                <p class="text-xs text-gray-400">{{ $booking->created_at->diffForHumans() }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full {{ $booking->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ ucfirst($booking->status) }}
            </span>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400">
            <i class="fas fa-inbox text-2xl mb-2"></i>
            <p class="text-sm">Belum ada aktivitas hari ini</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
