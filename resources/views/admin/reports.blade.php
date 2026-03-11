@extends('layouts.app')

@section('title', 'Reports')
@section('header', 'Reports')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="card">
        <h3 class="text-gray-700 font-bold mb-4">Generate Report</h3>
        <form action="{{ route('admin.student-logs.export') }}" method="GET">
            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-semibold mb-2">Report Type</label>
                <select name="type" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                    <option value="attendance">Student Logs</option>
                    <option value="students">Students Data</option>
                </select>
            </div>
             <div class="mb-4">
                <label class="block text-gray-600 text-sm font-semibold mb-2">Date Range</label>
                <div class="flex space-x-2">
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}" class="w-1/2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}" class="w-1/2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                </div>
            </div>
            
            <!-- View Summary Button -->
            <div class="mb-4">
                <button type="button" onclick="viewSummary()" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    View Course Summary
                </button>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-600 text-sm font-semibold mb-2">Export Format</label>
                <select name="format" id="format_select" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                    <option value="pdf">PDF Document (.pdf)</option>
                    <option value="csv">Excel / CSV (.csv)</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-emerald-800 text-white py-2 rounded hover:bg-emerald-900 transition flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Generate Report
            </button>
        </form>
    </div>
    
    <div id="preview_container" class="card bg-gray-50 border-dashed border-2 border-gray-300 overflow-hidden relative min-h-[300px]">
        @if($courseSummary && count($courseSummary) > 0)
            <!-- Course Summary Display -->
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Course Summary</h3>
                    <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-emerald-700 text-white text-sm">
                                <th class="py-3 px-4 rounded-tl-lg">#</th>
                                <th class="py-3 px-4">Course</th>
                                <th class="py-3 px-4 text-center">Total Logs</th>
                                <th class="py-3 px-4 text-center rounded-tr-lg">Unique Students</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($courseSummary as $index => $course)
                            <tr class="border-b border-gray-200 hover:bg-gray-100 transition {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="py-3 px-4 font-semibold text-gray-600">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-medium text-gray-800">{{ $course->course }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-semibold">
                                        {{ number_format($course->total_logs) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-semibold">
                                        {{ number_format($course->unique_students) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 font-bold text-gray-800">
                                <td colspan="2" class="py-3 px-4 text-right">Total:</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full">
                                        {{ number_format($courseSummary->sum('total_logs')) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full">
                                        {{ number_format($courseSummary->sum('unique_students')) }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                    <p class="text-sm text-blue-800">
                        <strong>Note:</strong> This summary shows library entry logs grouped by course for the selected date range.
                    </p>
                </div>
            </div>
        @else
            <!-- Default Preview Content -->
            <div class="flex items-center justify-center h-full">
                <div class="text-center text-gray-400 transition-all duration-500" id="preview_content">
                    <img src="{{ asset('images/pdf_export.png') }}" alt="PDF Format" class="max-w-[200px] h-auto drop-shadow-2xl animate-bounce-slow mx-auto">
                    <p class="mt-4 font-semibold text-gray-500">PDF Selection Preview</p>
                    @if(!$startDate || !$endDate)
                        <p class="mt-2 text-sm text-gray-400">Select a date range above to view course summary</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 4s ease-in-out infinite;
    }
    .fade-out {
        opacity: 0;
        transform: scale(0.95);
    }
    .fade-in {
        opacity: 1;
        transform: scale(1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formatSelect = document.getElementById('format_select');
        const previewContent = document.getElementById('preview_content');
        
        const previews = {
            'pdf': {
                'img': "{{ asset('images/pdf_export.png') }}",
                'text': 'PDF Selection Preview',
                'alt': 'PDF Format'
            },
            'csv': {
                'img': "{{ asset('images/csv_export.png') }}",
                'text': 'CSV Selection Preview',
                'alt': 'CSV Format'
            }
        };

        formatSelect.addEventListener('change', function() {
            const selection = this.value;
            const data = previews[selection];

            // Add fade-out transition
            previewContent.classList.add('fade-out');

            setTimeout(() => {
                previewContent.innerHTML = `
                    <img src="${data.img}" alt="${data.alt}" class="max-w-[200px] h-auto drop-shadow-2xl animate-bounce-slow">
                    <p class="mt-4 font-semibold text-gray-500">${data.text}</p>
                `;
                
                // Trigger fade-in
                previewContent.classList.remove('fade-out');
                previewContent.classList.add('fade-in');
                
                setTimeout(() => {
                    previewContent.classList.remove('fade-in');
                }, 500);
            }, 300);
        });
    });
    
    // View Summary function
    function viewSummary() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (!startDate || !endDate) {
            alert('Please select both start and end dates to view the course summary.');
            return;
        }
        
        // Navigate to reports page with date parameters
        window.location.href = `{{ route('admin.reports') }}?start_date=${startDate}&end_date=${endDate}`;
    }
</script>
@endsection
