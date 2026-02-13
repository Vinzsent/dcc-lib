@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Students -->
    <div class="card p-6 bg-white border-l-4 border-emerald-700 shadow-sm hover:shadow-md transition">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-emerald-100 text-emerald-700 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Master List</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalStudents) }}</p>
                <p class="text-xs text-emerald-600 mt-1">Total Students</p>
            </div>
        </div>
    </div>

    <!-- Active Now -->
    <div class="card p-6 bg-white border-l-4 border-blue-500 shadow-sm hover:shadow-md transition">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Active Now</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($activeNow) }}</p>
                <p class="text-xs text-blue-600 mt-1">Students Inside</p>
            </div>
        </div>
    </div>

    <!-- Total Daily Logs -->
    <div class="card p-6 bg-white border-l-4 border-yellow-500 shadow-sm hover:shadow-md transition">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Logs</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalLogs) }}</p>
                <p class="text-xs text-yellow-600 mt-1">Entries Recorded</p>
            </div>
        </div>
    </div>
    
    <!-- Pending alerts/placeholder -->
    <div class="card p-6 bg-white border-l-4 border-red-500 shadow-sm hover:shadow-md transition">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 14c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">System Alerts</p>
                <p class="text-2xl font-bold text-gray-800">0</p>
                <p class="text-xs text-red-600 mt-1">Healthy Status</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-700">Recent Library Entry</h3>
        <a href="{{ route('admin.student-logs') }}" class="text-emerald-700 hover:text-emerald-800 text-sm font-semibold">View All Logs &rarr;</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-400 text-xs uppercase tracking-widest border-b border-gray-100">
                    <th class="pb-3 font-semibold">Student</th>
                    <th class="pb-3 font-semibold">Dept, Course & Section</th>
                    <th class="pb-3 font-semibold">Time In</th>
                    <th class="pb-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($recentLogs as $log)
                <tr class="border-b border-gray-50 last:border-0">
                    <td class="py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs mr-3">
                                {{ strtoupper(substr($log->firstname, 0, 1) . substr($log->lastname, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $log->firstname }} {{ $log->lastname }}</p>
                                <p class="text-xs text-gray-400">ID: {{ $log->sid }} | RFID: {{ $log->rfid ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 text-gray-600">{{ $log->department }} | {{ $log->course }} {{ $log->section }} - {{ $log->year }}</td>
                    <td class="py-4 text-gray-600">{{ \Carbon\Carbon::parse($log->time_in)->diffForHumans() }}</td>
                    <td class="py-4">
                        @if(!$log->time_out)
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Inside</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Exited</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-400 italic">No recent activity found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
