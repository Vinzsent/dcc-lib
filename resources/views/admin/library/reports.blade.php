@extends('layouts.app')

@section('title', 'Library Reports')
@section('header', 'Library - Dashboard Analytics')

@section('content')

    {{-- ─── Export Button ─── --}}
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.library.reports.export') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Export to Excel
        </a>
    </div>

    <div class="space-y-6">

        {{-- ─── Row 1: Monthly Overview + Top Books ─── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Monthly Reports --}}
            <div class="card bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h3 class="text-gray-700 font-bold text-lg mb-4">Monthly Transactions Overview</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-indigo-50 text-indigo-800 uppercase text-xs leading-normal">
                                <th class="py-3 px-4">Month</th>
                                <th class="py-3 px-4 text-center">Borrowed</th>
                                <th class="py-3 px-4 text-center">Returned</th>
                                <th class="py-3 px-4 text-center">Overdue Active</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @forelse ($monthlyReport as $report)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-bold text-gray-700">{{ $report->month }}</td>
                                    <td class="py-3 px-4 text-center text-blue-600 font-semibold">
                                        {{ $report->total_borrowed }}</td>
                                    <td class="py-3 px-4 text-center text-green-600 font-semibold">
                                        {{ $report->total_returned }}</td>
                                    <td class="py-3 px-4 text-center text-red-600 font-semibold">
                                        {{ $report->total_overdue }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 px-4 text-center text-gray-400 italic">No sufficient data
                                        for monthly breakdown.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top Borrowed Books --}}
            <div class="card bg-white p-6 rounded-lg shadow-sm border border-orange-100">
                <h3 class="text-orange-800 font-bold text-lg mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                    </svg>
                    Top Borrowed Books
                </h3>
                <div class="space-y-3">
                    @forelse ($topBooks as $index => $top)
                        <div
                            class="flex items-center justify-between p-3 {{ $index < 3 ? 'bg-orange-50 border border-orange-200' : 'bg-gray-50' }} rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-8 w-8 rounded-full flex items-center justify-center font-bold text-sm
                            {{ $index === 0 ? 'bg-yellow-400 text-white' : ($index === 1 ? 'bg-gray-300 text-gray-700' : ($index === 2 ? 'bg-orange-300 text-white' : 'bg-gray-200 text-gray-500')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm flex items-center gap-2">
                                        {{ $top->book->title ?? 'Unknown Book' }}
                                        @if ((session('location') === 'Master' || session('location') === 'DCC BED') && isset($top->book->campus))
                                            <span class="text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">{{ $top->book->campus }}</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $top->book->author ?? 'Unknown Author' }}</div>
                                </div>
                            </div>
                            <div class="text-lg font-black text-orange-600">{{ $top->times_borrowed }}x</div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 italic py-4">No borrowing data yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ─── Row 2: Top Student Borrowers + Top Employee Borrowers ─── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Top Student Borrowers --}}
            <div class="card bg-white p-6 rounded-lg shadow-sm border border-blue-100">
                <h3 class="text-blue-800 font-bold text-lg mb-4 flex items-center gap-2">
                    <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                    Top Student Borrowers
                </h3>

                <div class="space-y-3">
                    @forelse ($topStudents as $index => $t)
                        @php
                            $s = $t->borrower;
                            $name = trim(
                                ($s->firstname ?? '') .
                                    ' ' .
                                    ($s->middlename ? $s->middlename[0] . '. ' : '') .
                                    ($s->lastname ?? ''),
                            );
                            $medal = match ($index) {
                                0 => ['bg-yellow-400', 'text-white'],
                                1 => ['bg-gray-300', 'text-gray-700'],
                                2 => ['bg-orange-300', 'text-white'],
                                default => ['bg-blue-100', 'text-blue-600'],
                            };
                        @endphp
                        <div
                            class="flex items-center gap-3 p-3 {{ $index < 3 ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50' }} rounded-lg">
                            {{-- Rank badge --}}
                            <div
                                class="h-9 w-9 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0 {{ $medal[0] }} {{ $medal[1] }}">
                                {{ $index + 1 }}
                            </div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 text-sm truncate flex items-center gap-2">
                                    {{ $name }}
                                    @if ((session('location') === 'Master' || session('location') === 'DCC BED') && isset($s->campus))
                                        <span class="text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">{{ $s->campus }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 truncate">
                                    {{ $s->course ?? 'No course' }}
                                    @if ($s->year)
                                        &bull; {{ $s->year }}{{ is_numeric($s->year) ? ' Year' : '' }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-300 font-mono">{{ $s->sid ?? '' }}</div>
                            </div>
                            {{-- Count --}}
                            <div class="flex-shrink-0 text-right">
                                <span class="text-lg font-black text-blue-600">{{ $t->total_borrowed }}</span>
                                <div class="text-xs text-gray-400">books</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 italic py-6">
                            <svg class="h-10 w-10 mx-auto mb-2 text-gray-200" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 14l9-5-9-5-9 5 9 5z" />
                            </svg>
                            No student borrow records yet.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Top Employee Borrowers --}}
            <div class="card bg-white p-6 rounded-lg shadow-sm border border-violet-100">
                <h3 class="text-violet-800 font-bold text-lg mb-4 flex items-center gap-2">
                    <svg class="h-6 w-6 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Top Employee Borrowers
                </h3>

                <div class="space-y-3">
                    @forelse ($topEmployees as $index => $t)
                        @php
                            $e = $t->borrower;
                            $name = trim(
                                ($e->firstname ?? '') .
                                    ' ' .
                                    ($e->middlename ? $e->middlename[0] . '. ' : '') .
                                    ($e->lastname ?? ''),
                            );
                            $medal = match ($index) {
                                0 => ['bg-yellow-400', 'text-white'],
                                1 => ['bg-gray-300', 'text-gray-700'],
                                2 => ['bg-orange-300', 'text-white'],
                                default => ['bg-violet-100', 'text-violet-600'],
                            };
                        @endphp
                        <div
                            class="flex items-center gap-3 p-3 {{ $index < 3 ? 'bg-violet-50 border border-violet-200' : 'bg-gray-50' }} rounded-lg">
                            {{-- Rank badge --}}
                            <div
                                class="h-9 w-9 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0 {{ $medal[0] }} {{ $medal[1] }}">
                                {{ $index + 1 }}
                            </div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 text-sm truncate flex items-center gap-2">
                                    {{ $name }}
                                    @if ((session('location') === 'Master' || session('location') === 'DCC BED') && isset($e->campus))
                                        <span class="text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">{{ $e->campus }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 truncate">
                                    {{ $e->position ?? 'No position' }}
                                    @if ($e->department)
                                        &bull; {{ $e->department }}
                                    @endif
                                </div>
                            </div>
                            {{-- Count --}}
                            <div class="flex-shrink-0 text-right">
                                <span class="text-lg font-black text-violet-600">{{ $t->total_borrowed }}</span>
                                <div class="text-xs text-gray-400">books</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 italic py-6">
                            <svg class="h-10 w-10 mx-auto mb-2 text-gray-200" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            No employee borrow records yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
