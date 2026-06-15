@extends('layouts.app')
@section('title', 'Paket Langganan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Paket Langganan</h1>
    <p class="text-gray-500 text-sm mt-1">Pilih paket terbaik untuk motor kamu</p>
</div>

<!-- Current Subscription -->
@if(isset($activeSubscription))
<div class="mb-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-5 md:p-6 text-white">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-blue-100 text-sm">Paket Aktif</p>
            <h3 class="text-xl font-bold mt-1">{{ ucfirst($activeSubscription->plan) }}</h3>
            <p class="text-blue-100 text-sm mt-2">
                <i class="fas fa-calendar mr-1"></i> Berlaku sampai {{ $activeSubscription->end_date->format('d M Y') }}
            </p>
        </div>
        <div class="text-left md:text-right">
            <p class="text-blue-100 text-sm">Sisa Cuci</p>
            <p class="text-3xl font-bold">
                {{ $activeSubscription->wash_limit ? ($activeSubscription->wash_limit - $activeSubscription->wash_used) : '∞' }}
            </p>
        </div>
    </div>
    <div class="mt-4">
        @if($activeSubscription->wash_limit)
            <div class="w-full bg-white bg-opacity-20 rounded-full h-3">
                <div class="bg-white h-3 rounded-full transition-all" style="width: {{ ($activeSubscription->wash_used / $activeSubscription->wash_limit) * 100 }}%"></div>
            </div>
            <p class="text-xs text-blue-100 mt-2">{{ $activeSubscription->wash_used }}/{{ $activeSubscription->wash_limit }} cuci terpakai</p>
        @endif
    </div>
</div>
@endif

<!-- Pricing Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
    <!-- Basic -->
    <div class="card-hover bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-droplet text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Basic</h3>
            <p class="text-gray-400 text-sm mt-1">Untuk cuci rutin</p>
            <div class="mt-4">
                <span class="text-3xl font-bold text-gray-800">99K</span>
                <span class="text-gray-400 text-sm">/bulan</span>
            </div>
            <ul class="mt-6 space-y-3">
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> 4x cuci motor
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> Booking prioritas
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> Cuci standar
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-300">
                    <i class="fas fa-times"></i> Cuci premium
                </li>
            </ul>
        </div>
        <div class="p-6 pt-0">
            <form method="POST" action="/subscription/subscribe">
                @csrf
                <input type="hidden" name="plan" value="basic">
                <button class="w-full py-3 bg-blue-50 text-blue-600 font-semibold rounded-lg hover:bg-blue-100 transition">
                    Pilih Basic
                </button>
            </form>
        </div>
    </div>

    <!-- Standard (Popular) -->
    <div class="card-hover bg-white rounded-xl shadow-sm border-2 border-blue-500 overflow-hidden relative">
        <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
            POPULER
        </div>
        <div class="p-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-star text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Standard</h3>
            <p class="text-gray-400 text-sm mt-1">Paling banyak dipilih</p>
            <div class="mt-4">
                <span class="text-3xl font-bold text-gray-800">179K</span>
                <span class="text-gray-400 text-sm">/bulan</span>
            </div>
            <ul class="mt-6 space-y-3">
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> 8x cuci motor
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> Booking prioritas
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> Cuci standar + premium
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> Diskon 10% extra
                </li>
            </ul>
        </div>
        <div class="p-6 pt-0">
            <form method="POST" action="/subscription/subscribe">
                @csrf
                <input type="hidden" name="plan" value="standard">
                <button class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Pilih Standard
                </button>
            </form>
        </div>
    </div>

    <!-- Premium -->
    <div class="card-hover bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                <i class="fas fa-crown text-purple-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Premium</h3>
            <p class="text-gray-400 text-sm mt-1">Unlimited tanpa batas</p>
            <div class="mt-4">
                <span class="text-3xl font-bold text-gray-800">299K</span>
                <span class="text-gray-400 text-sm">/bulan</span>
            </div>
            <ul class="mt-6 space-y-3">
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> Unlimited cuci
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> Prioritas utama
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> Semua jenis layanan
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-check text-green-500"></i> Free wax bulanan
                </li>
            </ul>
        </div>
        <div class="p-6 pt-0">
            <form method="POST" action="/subscription/subscribe">
                @csrf
                <input type="hidden" name="plan" value="premium">
                <button class="w-full py-3 bg-purple-50 text-purple-600 font-semibold rounded-lg hover:bg-purple-100 transition">
                    Pilih Premium
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
