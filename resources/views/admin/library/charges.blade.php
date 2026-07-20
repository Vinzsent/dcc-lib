@extends('layouts.app')

@section('title', 'Overdue Charges')
@section('header', 'Library - Overdue Charges')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Active Overdue Cards -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between transition hover:shadow-md">
            <div class="space-y-2">
                <span class="text-sm text-gray-500 font-medium uppercase tracking-wider block">Active Overdue Books</span>
                <span class="text-3xl font-bold text-gray-900">{{ $totalActiveOverdueCount }}</span>
                <span class="text-xs text-orange-600 block">Pending Return & Settlement</span>
            </div>
            <div class="p-4 bg-orange-50 text-orange-600 rounded-2xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 14c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <!-- Incurred Fines Collected -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between transition hover:shadow-md">
            <div class="space-y-2">
                <span class="text-sm text-gray-500 font-medium uppercase tracking-wider block">Total Fines Recorded</span>
                <span class="text-3xl font-bold text-gray-900">₱{{ number_format($totalFinesSum, 2) }}</span>
                <span class="text-xs text-green-600 block">From Late Returns</span>
            </div>
            <div class="p-4 bg-green-50 text-green-600 rounded-2xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & List Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Panel Header / Search & Filters -->
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Charges Listing</h2>
                <p class="text-sm text-gray-500 mt-1">Track unreturned overdue books and history of fines.</p>
            </div>

            <form action="{{ route('admin.library.charges.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Search bar -->
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search Borrower, Book..." 
                        class="pl-9 pr-3 py-2 w-full border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition outline-none">
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-48">
                    <select name="status" class="py-2 px-3 w-full border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition outline-none">
                        <option value="">All Charges</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Overdue</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned with Fines</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="submit" class="flex-grow sm:flex-none px-4 py-2 bg-primary hover:bg-secondary text-white font-medium text-sm rounded-lg transition duration-200 flex items-center justify-center gap-2 shadow-sm">
                        <span>Search</span>
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.library.charges.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition duration-200 text-center">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider font-mono">
                        <th class="py-4 px-6">Borrower</th>
                        <th class="py-4 px-6">Book Details</th>
                        <th class="py-4 px-6">Timeline</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Fine Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($transactions as $txn)
                        <tr class="hover:bg-gray-50/55 transition">
                            <!-- Borrower Details -->
                            <td class="py-4 px-6">
                                @if($txn->borrower_details)
                                    <div class="font-semibold text-gray-900">
                                        {{ $txn->borrower_details->firstname }} {{ $txn->borrower_details->lastname }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        ID: {{ $txn->borrower_id }} | {{ str_replace('App\\Models\\', '', $txn->borrower_type) }}
                                    </div>
                                    @if(isset($txn->borrower_details->department))
                                        <div class="text-xs text-gray-400">
                                            {{ $txn->borrower_details->department }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-red-500 italic">Borrower info missing</span>
                                    <div class="text-xs text-gray-500 mt-0.5">ID: {{ $txn->borrower_id }}</div>
                                @endif
                            </td>

                            <!-- Book Details -->
                            <td class="py-4 px-6 max-w-xs">
                                <div class="font-medium text-gray-900 truncate" title="{{ $txn->book->title ?? 'Book Deleted' }}">
                                    {{ $txn->book->title ?? 'Book Deleted' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    Accession No: {{ $txn->accession_no }}
                                </div>
                                <div class="inline-flex items-center gap-1.5 mt-1 px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                    <span>Section: {{ $txn->book_section }}</span>
                                </div>
                            </td>

                            <!-- Timeline -->
                            <td class="py-4 px-6 space-y-1">
                                <div class="text-xs flex items-center gap-1.5 text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    <span>Borrowed: {{ \Carbon\Carbon::parse($txn->date_borrowed)->format('M d, Y h:i A') }}</span>
                                </div>
                                <div class="text-xs flex items-center gap-1.5 text-red-500 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    <span>Due: {{ \Carbon\Carbon::parse($txn->due_date)->format('M d, Y h:i A') }}</span>
                                </div>
                                @if($txn->date_returned)
                                    <div class="text-xs flex items-center gap-1.5 text-green-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        <span>Returned: {{ \Carbon\Carbon::parse($txn->date_returned)->format('M d, Y h:i A') }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-6">
                                @if($txn->is_active_overdue)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 border border-red-100 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-ping"></span>
                                        Unreturned (Overdue)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 border border-green-100 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                                        Returned (Late)
                                    </span>
                                @endif
                            </td>

                            <!-- Fine Amount -->
                            <td class="py-4 px-6 text-right font-bold text-lg {{ $txn->is_active_overdue ? 'text-orange-600' : 'text-gray-900' }}">
                                ₱{{ number_format($txn->calculated_fine, 2) }}
                                @if($txn->is_active_overdue)
                                    <div class="text-[10px] font-normal text-orange-500 mt-0.5">Est. Pending</div>
                                @else
                                    <div class="text-[10px] font-normal text-green-500 mt-0.5">Settled Fine</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-sm">No charges found matching filters.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="p-6 border-t border-gray-100 pagination-container">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
