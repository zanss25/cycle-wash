@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-lg mx-auto">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-credit-card text-green-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Pembayaran</h1>
            <p class="text-gray-500">Booking #{{ $booking->queue_number }}</p>
        </div>

        {{-- Detail --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Layanan</span>
                    <span class="font-medium">{{ $booking->service->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Plat Nomor</span>
                    <span class="font-medium">{{ $booking->plate_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                </div>
                <hr>
                <div class="flex justify-between text-lg">
                    <span class="font-bold">Total</span>
                    <span class="font-bold text-blue-600">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Form Pilih Metode --}}
        <form action="{{ route('booking.payment.process', $booking) }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 space-y-3">
                <h2 class="font-semibold text-gray-700 mb-3">Pilih Metode Pembayaran</h2>

                <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer hover:border-blue-400 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                    <input type="radio" name="payment_method" value="cash" required class="text-blue-600">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold">Cash</p>
                        <p class="text-xs text-gray-500">Bayar di kasir</p>
                    </div>
                </label>

                <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer hover:border-blue-400 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                    <input type="radio" name="payment_method" value="transfer" class="text-blue-600">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-university text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold">Transfer Bank</p>
                        <p class="text-xs text-gray-500">BCA, BRI, Mandiri</p>
                    </div>
                </label>

                <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer hover:border-blue-400 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                    <input type="radio" name="payment_method" value="ewallet" class="text-blue-600">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold">E-Wallet</p>
                        <p class="text-xs text-gray-500">GoPay, OVO, Dana</p>
                    </div>
                </label>

                <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer hover:border-blue-400 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                    <input type="radio" name="payment_method" value="qris" class="text-blue-600">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-qrcode text-orange-600"></i>
                    </div>
                    <div>
                        <p class="font-semibold">QRIS</p>
                        <p class="text-xs text-gray-500">Scan QR Code</p>
                    </div>
                </label>

                @error('payment_method')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                <i class="fas fa-lock"></i>
                Bayar Sekarang
            </button>

            <a href="{{ route('bookings.index') }}" class="block text-center text-gray-500 mt-4 text-sm hover:text-gray-700">
                ← Kembali
            </a>
        </form>
    </div>
</div>
@endsection
