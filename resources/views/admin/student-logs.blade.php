@extends('layouts.app')

@section('title', 'Student Logs')
@section('header', 'Student Logs')

@section('content')
<div class="card">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h3 class="text-gray-700 font-bold text-lg">Daily Logs</h3>
        
        <!-- Search Bar -->
        <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <form method="GET" action="{{ route('admin.student-logs') }}">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full py-2 pl-10 pr-4 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600 focus:ring-1 focus:ring-green-600 transition" 
                        placeholder="Search logs...">
                </form>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="overflow-x-auto">
        <form id="filterForm" method="GET" action="{{ route('admin.student-logs') }}">
            <!-- Keep existing global search -->
            <input type="hidden" name="search" value="{{ request('search') }}">
            
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'firstname', 'direction' => request('sort') == 'firstname' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Student
                                @include('partials.sort-icon', ['field' => 'firstname'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'campus', 'direction' => request('sort') == 'campus' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Campus
                                @include('partials.sort-icon', ['field' => 'campus'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'department', 'direction' => request('sort') == 'department' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Department
                                @include('partials.sort-icon', ['field' => 'department'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <div class="flex flex-col">
                                <span class="flex items-center gap-1">Course, Section & Year</span>
                                <div class="flex gap-2 text-[10px] lowercase font-normal mt-1">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'course', 'direction' => request('sort') == 'course' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="hover:text-green-600 flex items-center gap-0.5">Course @include('partials.sort-icon', ['field' => 'course'])</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'section', 'direction' => request('sort') == 'section' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="hover:text-green-600 flex items-center gap-0.5">Section @include('partials.sort-icon', ['field' => 'section'])</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'year', 'direction' => request('sort') == 'year' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="hover:text-green-600 flex items-center gap-0.5 font-bold">Year @include('partials.sort-icon', ['field' => 'year'])</a>
                                </div>
                            </div>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'time_in', 'direction' => request('sort') == 'time_in' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Date
                                @include('partials.sort-icon', ['field' => 'time_in'])
                            </a>
                        </th>
                        <th class="py-3 px-6">Time In</th>
                        <th class="py-3 px-6">Time Out</th>
                    </tr>
                    <!-- Filter Row -->
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <td class="py-2 px-6">
                            <!-- Student column filter is global search usually, but we can add specific name search if needed -->
                        </td>
                        <td class="py-2 px-6">
                            <select name="campus" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                                <option value="">All</option>
                                @foreach($campuses as $c)
                                    <option value="{{ $c }}" {{ request('campus') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-2 px-6">
                            <select name="department" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                                <option value="">All</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d }}" {{ request('department') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-2 px-6">
                            <div class="flex gap-1">
                                <input type="text" name="course" value="{{ request('course') }}" placeholder="Course" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                                <select name="year" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                                    <option value="">Year</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td class="py-2 px-6"></td>
                        <td class="py-2 px-6"></td>
                        <td class="py-2 px-6">
                            <button type="submit" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition">Filter</button>
                        </td>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($logs as $log)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="py-4 px-6 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3">
                                    {{ strtoupper(substr($log->firstname, 0, 1) . substr($log->lastname, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 block">{{ $log->firstname }} {{ $log->middlename ? $log->middlename . ' ' : '' }}{{ $log->lastname }}</span>
                                    <span class="text-xs text-gray-400">ID: {{ $log->sid }} | RFID: {{ $log->rfid ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ $log->campus }}
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ $log->department }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $log->course }} {{ $log->section }} - {{ $log->year }}
                        </td>
                        <td class="py-4 px-6">
                            {{ \Carbon\Carbon::parse($log->time_in)->format('M d, Y') }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-semibold">
                                {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            @if($log->time_out)
                                <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-semibold">
                                    {{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}
                                </span>
                            @else
                                <span class="text-gray-400 italic">Active</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 px-6 text-center text-gray-400">
                            No logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>
    <!-- Pagination -->
    <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-100 pt-6">
        <div class="text-sm text-gray-500">
            Showing <span class="font-semibold text-gray-700">{{ $logs->firstItem() ?? 0 }}</span> 
            to <span class="font-semibold text-gray-700">{{ $logs->lastItem() ?? 0 }}</span> 
            of <span class="font-semibold text-gray-700">{{ $logs->total() }}</span> logs
        </div>
        <div class="pagination-container">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
