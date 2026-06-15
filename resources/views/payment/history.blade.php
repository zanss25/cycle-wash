@extends('layouts.app')
@section('title', 'Saldo & Riwayat')
@section('content')
<h1 class="text-2xl font-bold mb-6">Saldo & Riwayat Pembayaran</h1>

<!-- Saldo -->
<div class="bg-white rounded-lg shadow p-6 mb-6 flex justify-between items-center">
    <div>
        <p class="text-gray-500">Saldo Wallet</p>
        <p class="text-3xl font-bold text-green-600">Rp {{ number_format($balance) }}</p>
    </div>
    <form action="{{ route('payment.topup') }}" method="POST" class="flex gap-2">
        @csrf
        <input type="number" name="amount" placeholder="Jumlah" min="10000" step="5000" required
            class="border rounded px-3 py-2 w-40">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Top Up</button>
    </form>
</div>

<!-- Riwayat -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3">Tanggal</th>
                <th>Metode</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr class="border-b">
                <td class="px-4 py-3">{{ $payment->created_at->format('d M Y H:i') }}</td>
                <td>{{ $payment->payment_method }}</td>
                <td class="font-bold">Rp {{ number_format($payment->amount) }}</td>
                <td>
                    <span class="px-2 py-1 rounded text-xs
                        @if($payment->status == 'completed') bg-green-100 text-green-700
                        @elseif($payment->status == 'refunded') bg-red-100 text-red-700
                        @else bg-yellow-100 text-yellow-700
                        @endif">
                        {{ $payment->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Belum ada transaksi</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3">{{ $payments->links() }}</div>
</div>
@endsection
