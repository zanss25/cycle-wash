@extends('layouts.app')

@section('title', 'Queue History')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Queue History</h1>
            <p class="text-sm text-gray-500 mt-1">View past queue records and statistics</p>
        </div>
        <div class="flex items-center gap-2">
            <input type="date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Served</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalServed ?? 142 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Avg Wait Time</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $avgWait ?? '8 min' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">No Shows</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $noShows ?? 5 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Satisfaction</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $satisfaction ?? '94%' }}</p>
        </div>
    </div>

    {{-- History Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">#</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Customer</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 hidden sm:table-cell">Service</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">Joined</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Wait</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($histories ?? [] as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-mono text-gray-500">{{ $item->queue_number }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->customer_name }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $item->service }}</td>
                        <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $item->created_at->format('h:i A') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $item->wait_time }} min</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                {{ $item->status === 'served' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            No history records found for this date.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($histories) && $histories->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
