@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Booking Baru</h1>
            <p class="text-gray-500">Isi form di bawah untuk membuat booking cuci motor</p>
        </div>

        {{-- Tampilkan error validasi --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <ul class="text-red-600 text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('booking.store') }}" method="POST" class="bg-white rounded-xl shadow-sm p-6 space-y-5">
            @csrf

            {{-- Layanan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Layanan</label>
                <select name="service_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3">
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Plat Nomor --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor</label>
                <input type="text" name="plate_number" value="{{ old('plate_number') }}" required
                       placeholder="Contoh: B 1234 XYZ"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3">
            </div>

            {{-- Tipe Motor --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Motor</label>
                <select name="vehicle_type" required class="w-full border border-gray-300 rounded-lg px-4 py-3">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="matic" {{ old('vehicle_type') == 'matic' ? 'selected' : '' }}>Matic</option>
                    <option value="sport" {{ old('vehicle_type') == 'sport' ? 'selected' : '' }}>Sport</option>
                    <option value="bebek" {{ old('vehicle_type') == 'bebek' ? 'selected' : '' }}>Bebek</option>
                    <option value="trail" {{ old('vehicle_type') == 'trail' ? 'selected' : '' }}>Trail</option>
                </select>
            </div>

            {{-- Tanggal Booking --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Booking</label>
                <input type="date" name="booking_date" value="{{ old('booking_date') }}" required
                       min="{{ date('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3">
            </div>

            {{-- Jam Kedatangan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Kedatangan</label>
                <input type="time" name="booking_time" value="{{ old('booking_time') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3">
            </div>

            {{-- Metode Pembayaran --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select name="payment_method" required class="w-full border border-gray-300 rounded-lg px-4 py-3">
                    <option value="">-- Pilih Metode --</option>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="ewallet" {{ old('payment_method') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="wallet" {{ old('payment_method') == 'wallet' ? 'selected' : '' }}>Saldo Wallet</option>
                    <option value="subscription" {{ old('payment_method') == 'subscription' ? 'selected' : '' }}>Langganan</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold flex items-center justify-center gap-2">
                <i class="fas fa-plus-circle"></i> Buat Booking
            </button>
        </form>
    </div>
</div>
@endsection
