@extends('layouts.app')

@section('title', 'Employee Logs')
@section('header', 'Employee Logs')

@section('content')
<div class="card">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h3 class="text-gray-700 font-bold text-lg">Library Entry Logs - Employees</h3>
        
        <div class="relative w-full md:w-80">
            <form method="GET" action="{{ route('admin.employee-logs') }}">
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

    <div class="overflow-x-auto">
        <form id="filterForm" method="GET" action="{{ route('admin.employee-logs') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'firstname', 'direction' => request('sort') == 'firstname' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Employee
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
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'position', 'direction' => request('sort') == 'position' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Position
                                @include('partials.sort-icon', ['field' => 'position'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'employment_type', 'direction' => request('sort') == 'employment_type' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Type
                                @include('partials.sort-icon', ['field' => 'employment_type'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'time_in', 'direction' => request('sort') == 'time_in' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Time In
                                @include('partials.sort-icon', ['field' => 'time_in'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'time_out', 'direction' => request('sort') == 'time_out' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Time Out
                                @include('partials.sort-icon', ['field' => 'time_out'])
                            </a>
                        </th>
                        <th class="py-3 px-6">Status</th>
                    </tr>
                    <!-- Filter Row -->
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <td class="py-2 px-6">
                            <!-- Empty for Employee column -->
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
                            <input type="text" name="position" value="{{ request('position') }}" placeholder="Position" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                        </td>
                        <td class="py-2 px-6">
                            <select name="employment_type" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                                <option value="">All</option>
                                @foreach($employmentTypes as $type)
                                    <option value="{{ $type }}" {{ request('employment_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-2 px-6" colspan="3">
                            <button type="submit" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition">Filter</button>
                        </td>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($logs as $log)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-800">{{ $log->firstname }} {{ $log->lastname }}</span>
                                <span class="text-xs text-gray-400">ID: {{ $log->eid }} | RFID: {{ $log->rfid ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ $log->campus ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $log->department ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-semibold">
                                {{ $log->position ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            {{ $log->employment_type ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ \Carbon\Carbon::parse($log->time_in)->format('M d, Y h:i A') }}
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('M d, Y h:i A') : '-' }}
                        </td>
                        <td class="py-4 px-6">
                            @if(!$log->time_out)
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Inside</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Exited</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-400 italic">
                            No employee logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-100 pt-6">
        <div class="text-sm text-gray-600">
            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
        </div>
        <div class="pagination-container">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
