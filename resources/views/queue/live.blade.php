@extends('layouts.app')
@section('title', 'Antrian Live')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Antrian Hari Ini</h1>
        <p class="text-gray-500 text-sm mt-1">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <div class="mt-3 md:mt-0">
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Live Update
        </span>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="card-hover bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Dalam Antrian</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $queue['total_in_queue'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-blue-600"></i>
            </div>
        </div>
        <div class="mt-3 flex items-center text-xs text-gray-400">
            <i class="fas fa-clock mr-1"></i> Diupdate otomatis
        </div>
    </div>

    <div class="card-hover bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Est. Selesai Semua</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $queue['estimated_clear_time'] }} <span class="text-lg">min</span></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-hourglass-half text-green-600"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="w-full bg-green-100 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(100, $queue['total_in_queue'] * 10) }}%"></div>
            </div>
        </div>
    </div>

    <div class="card-hover bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Rata-rata / Motor</p>
                <p class="text-3xl font-bold text-purple-600 mt-1">~30 <span class="text-lg">min</span></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-motorcycle text-purple-600"></i>
            </div>
        </div>
        <div class="mt-3 text-xs text-gray-400">
            <i class="fas fa-info-circle mr-1"></i> Berdasarkan data hari ini
        </div>
    </div>
</div>

<!-- Queue List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 md:p-6 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-list mr-2 text-blue-500"></i>Daftar Antrian
        </h2>
        <div class="flex gap-2">
            <button onclick="refreshQueue()" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm hover:bg-blue-100 transition">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
        </div>
    </div>

    @if($queue['bookings']->count() > 0)
        <!-- Mobile Cards -->
        <div class="md:hidden divide-y">
            @foreach($queue['bookings'] as $index => $booking)
            <div class="p-4 flex items-center gap-4 {{ $index === 0 ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                    {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                    #{{ $index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 truncate">{{ $booking->user->name ?? 'Customer' }}</p>
                    <p class="text-sm text-gray-500">{{ $booking->service->name ?? 'Cuci Motor' }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                        {{ $booking->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $booking->status === 'in_progress' ? 'Proses' : 'Menunggu' }}
                    </span>
                    <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($queue['bookings'] as $index => $booking)
                    <tr class="hover:bg-gray-50 transition {{ $index === 0 ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4">
                            <span class="w-8 h-8 inline-flex items-center justify-center rounded-full font-bold text-sm
                                {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $booking->user->name ?? 'Customer' }}</p>
                                    <p class="text-xs text-gray-400">{{ $booking->user->phone ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->service->name ?? 'Cuci Motor' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
                                {{ $booking->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                <i class="fas {{ $booking->status === 'in_progress' ? 'fa-spinner fa-spin' : 'fa-clock' }} mr-1"></i>
                                {{ $booking->status === 'in_progress' ? 'Sedang Dicuci' : 'Menunggu' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                <form method="POST" action="/queue/{{ $booking->id }}/start" class="inline">
                                    @csrf
                                    <button class="px-3 py-1 bg-green-500 text-white rounded-lg text-xs hover:bg-green-600 transition">
                                        <i class="fas fa-play mr-1"></i> Mulai
                                    </button>
                                </form>
                            @elseif($booking->status === 'in_progress')
                                <form method="POST" action="/queue/{{ $booking->id }}/complete" class="inline">
                                    @csrf
                                    <button class="px-3 py-1 bg-blue-500 text-white rounded-lg text-xs hover:bg-blue-600 transition">
                                        <i class="fas fa-check mr-1"></i> Selesai
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-inbox text-gray-300 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-500">Belum Ada Antrian</h3>
            <p class="text-sm text-gray-400 mt-1">Antrian hari ini masih kosong</p>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    function refreshQueue() {
        location.reload();
    }
    // Auto refresh setiap 30 detik
    setInterval(() => { location.reload(); }, 30000);
</script>
@endpush
