@extends('layouts.app')

@section('title', 'Library Transaction History')
@section('header', 'Library - Borrowing History')

@section('content')
<div class="card bg-white p-6 rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h3 class="text-gray-700 font-bold text-lg">Transaction History</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6">Date Borrowed</th>
                    <th class="py-3 px-6">Borrower</th>
                    <th class="py-3 px-6">Book Title</th>
                    @if (session('location') === 'Master' || session('location') === 'DCC BED')
                        <th class="py-3 px-6">Campus</th>
                    @endif
                    <th class="py-3 px-6">Status</th>
                    <th class="py-3 px-6">Due Date</th>
                    <th class="py-3 px-6">Returned On</th>
                    <th class="py-3 px-6">Fine</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse ($transactions as $txn)
                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                    <td class="py-4 px-6">{{ \Carbon\Carbon::parse($txn->date_borrowed)->format('M d, Y') }}</td>
                    <td class="py-4 px-6 font-medium text-gray-800">
                        {{ $txn->borrower->firstname ?? 'Unknown' }} {{ $txn->borrower->lastname ?? '' }}
                        <div class="text-xs text-gray-400">{{ class_basename($txn->borrower_type) }} ({{ $txn->borrower_id }})</div>
                    </td>
                    <td class="py-4 px-6">
                        {{ $txn->book->title ?? 'Deleted Book' }}
                        <div class="text-xs text-gray-400">#{{ $txn->accession_no }}</div>
                    </td>
                    @if (session('location') === 'Master' || session('location') === 'DCC BED')
                        <td class="py-4 px-6">
                            <span class="font-medium text-purple-700">{{ $txn->book->campus ?? 'N/A' }}</span>
                        </td>
                    @endif
                    <td class="py-4 px-6">
                        @if($txn->status === 'Returned')
                            <span class="bg-gray-100 text-gray-700 py-1 px-3 rounded-full text-xs">{{ $txn->status }}</span>
                        @elseif($txn->status === 'Overdue' || \Carbon\Carbon::today()->gt(\Carbon\Carbon::parse($txn->due_date)) && $txn->status === 'Borrowed')
                            <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs">Overdue</span>
                        @else
                            <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs">{{ $txn->status }}</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">{{ \Carbon\Carbon::parse($txn->due_date)->format('M d, Y') }}</td>
                    <td class="py-4 px-6">{{ $txn->date_returned ? \Carbon\Carbon::parse($txn->date_returned)->format('M d, Y') : '-' }}</td>
                    <td class="py-4 px-6 text-red-600 font-semibold text-center">
                        {{ $txn->fine > 0 ? '₱' . number_format($txn->fine, 2) : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ (session('location') === 'Master' || session('location') === 'DCC BED') ? 8 : 7 }}" class="py-4 px-6 text-center text-gray-400 italic">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
