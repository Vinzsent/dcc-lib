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
            <div id="date_range_container" class="mb-4">
                <label class="block text-gray-600 text-sm font-semibold mb-2">Date Range</label>
                <div class="flex space-x-2">
                    <p class="text-gray-600 text-sm font-semibold mb-2">Start Date</p>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}" class="w-1/2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                    <p class="text-gray-600 text-sm font-semibold mb-2">End Date</p>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}" class="w-1/2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                </div>
            </div>

            <div id="student_filters_container" class="mb-4 hidden">
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-semibold mb-2">Course</label>
                    <select name="course" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course }}">{{ $course }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-semibold mb-2">Year</label>
                    <select name="year" id="student_year" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Preview Student Data Button -->
                <div class="mb-4">
                    <button type="button" onclick="previewStudentData()" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview Student Data
                    </button>
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
            <div id="course_summary_view" class="p-6">
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
            <!-- Default Preview Content / Student Preview Container -->
            <div id="dynamic_preview_content" class="h-full">
                <div class="flex items-center justify-center h-full p-6">
                    <div class="text-center text-gray-400 transition-all duration-500" id="preview_content">
                        <img src="{{ asset('images/pdf_export.png') }}" alt="PDF Format" class="max-w-[200px] h-auto drop-shadow-2xl animate-bounce-slow mx-auto">
                        <p class="mt-4 font-semibold text-gray-500">PDF Selection Preview</p>
                        @if(!$startDate || !$endDate)
                            <p class="mt-2 text-sm text-gray-400">Select parameters to view preview</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div id="studentPreviewModal" class="hidden"></div>

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

        const typeSelect = document.querySelector('select[name="type"]');
        const dateRangeContainer = document.getElementById('date_range_container');
        const studentFiltersContainer = document.getElementById('student_filters_container');
        const viewSummaryBtn = document.querySelector('button[onclick="viewSummary()"]').closest('.mb-4');

        window.resetPreview = function() {
            const previewContainer = document.getElementById('preview_container');
            previewContainer.innerHTML = `
                <div id="dynamic_preview_content" class="h-full">
                    <div class="flex items-center justify-center h-full p-6">
                        <div class="text-center text-gray-400 transition-all duration-500" id="preview_content">
                            <img src="${previews[formatSelect.value].img}" alt="${previews[formatSelect.value].alt}" class="max-w-[200px] h-auto drop-shadow-2xl animate-bounce-slow mx-auto">
                            <p class="mt-4 font-semibold text-gray-500">${previews[formatSelect.value].text}</p>
                            <p class="mt-2 text-sm text-gray-400">Select parameters to view preview</p>
                        </div>
                    </div>
                </div>
            `;
        };

        function toggleFilters() {
            window.resetPreview();
            if (typeSelect.value === 'students') {
                dateRangeContainer.classList.add('hidden');
                studentFiltersContainer.classList.remove('hidden');
                viewSummaryBtn.classList.add('hidden');
            } else {
                dateRangeContainer.classList.remove('hidden');
                studentFiltersContainer.classList.add('hidden');
                viewSummaryBtn.classList.remove('hidden');
            }
        }

        typeSelect.addEventListener('change', toggleFilters);
        toggleFilters(); // Initialize on load

        formatSelect.addEventListener('change', function() {
            const selection = this.value;
            const data = previews[selection];
            const previewContent = document.getElementById('preview_content');
            if (!previewContent) return;

            previewContent.classList.add('fade-out');
            setTimeout(() => {
                previewContent.innerHTML = `
                    <img src="${data.img}" alt="${data.alt}" class="max-w-[200px] h-auto drop-shadow-2xl animate-bounce-slow mx-auto">
                    <p class="mt-4 font-semibold text-gray-500">${data.text}</p>
                `;
                previewContent.classList.remove('fade-out');
                previewContent.classList.add('fade-in');
                setTimeout(() => previewContent.classList.remove('fade-in'), 500);
            }, 300);
        });

        // Auto-preview when both dates are selected
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function autoPreviewSummary() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            if (!startDate || !endDate) return;
            if (typeSelect.value !== 'attendance') return;
            loadCourseSummaryPreview(startDate, endDate);
        }

        startDateInput.addEventListener('change', autoPreviewSummary);
        endDateInput.addEventListener('change', autoPreviewSummary);
    });

    // View Summary button handler
    function viewSummary() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (!startDate || !endDate) {
            alert('Please select both start and end dates to view the course summary.');
            return;
        }

        loadCourseSummaryPreview(startDate, endDate);
    }

    function loadCourseSummaryPreview(startDate, endDate) {
        const previewContainer = document.getElementById('preview_container');

        previewContainer.innerHTML = `
            <div class="flex items-center justify-center h-full p-12">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-emerald-800 mb-4"></div>
                    <p class="text-gray-500 font-semibold">Loading summary...</p>
                </div>
            </div>
        `;

        fetch(`{{ route('admin.reports') }}?start_date=${startDate}&end_date=${endDate}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (!data || data.length === 0) {
                previewContainer.innerHTML = `
                    <div class="flex items-center justify-center h-full p-12">
                        <div class="text-center text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-4 font-semibold text-gray-500">No data found for selected date range.</p>
                            <p class="mt-1 text-sm text-gray-400">${formatDateDisplay(startDate)} – ${formatDateDisplay(endDate)}</p>
                        </div>
                    </div>
                `;
                return;
            }

            const totalLogs = data.reduce((sum, r) => sum + Number(r.total_logs), 0);
            const totalStudents = data.reduce((sum, r) => sum + Number(r.unique_students), 0);

            const rows = data.map((course, index) => `
                <tr class="border-b border-gray-200 hover:bg-gray-100 transition ${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                    <td class="py-3 px-4 font-semibold text-gray-600">${index + 1}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">${course.course}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-semibold">${Number(course.total_logs).toLocaleString()}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-semibold">${Number(course.unique_students).toLocaleString()}</span>
                    </td>
                </tr>
            `).join('');

            previewContainer.innerHTML = `
                <div class="p-6 h-full flex flex-col">
                    <div class="flex items-center justify-between mb-4 flex-shrink-0">
                        <h3 class="text-lg font-bold text-gray-800">Course Summary</h3>
                        <span class="text-sm text-gray-500">${formatDateDisplay(startDate)} – ${formatDateDisplay(endDate)}</span>
                    </div>
                    <div class="overflow-y-auto flex-grow shadow border-b border-gray-200 sm:rounded-lg custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-emerald-700 text-white text-sm sticky top-0 z-10">
                                <tr>
                                    <th class="py-3 px-4 rounded-tl-lg">#</th>
                                    <th class="py-3 px-4">Course</th>
                                    <th class="py-3 px-4 text-center">Total Logs</th>
                                    <th class="py-3 px-4 text-center rounded-tr-lg">Unique Students</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">${rows}</tbody>
                            <tfoot>
                                <tr class="bg-gray-100 font-bold text-gray-800">
                                    <td colspan="2" class="py-3 px-4 text-right">Total:</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full">${totalLogs.toLocaleString()}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full">${totalStudents.toLocaleString()}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded flex-shrink-0">
                        <p class="text-sm text-blue-800">
                            <strong>Note:</strong> This summary shows library entry logs grouped by course for the selected date range.
                        </p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error fetching summary preview:', error);
            previewContainer.innerHTML = `
                <div class="flex items-center justify-center h-full p-12">
                    <div class="text-center text-red-500">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-4 font-bold">Failed to load preview.</p>
                        <button onclick="viewSummary()" class="mt-4 text-emerald-800 font-bold hover:underline">Retry</button>
                    </div>
                </div>
            `;
        });
    }

    function formatDateDisplay(dateStr) {
        const d = new Date(dateStr + 'T00:00:00');
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    // Render Pagination Controls
    function renderPagination(data) {
        if (data.last_page <= 1) return '';
        
        const prevDisabled = data.current_page === 1 ? 'disabled' : '';
        const nextDisabled = data.current_page === data.last_page ? 'disabled' : '';
        
        // Generate page number buttons (desktop)
        let pageButtons = '';
        const maxVisible = 5;
        let startPage = Math.max(1, data.current_page - 2);
        let endPage = Math.min(data.last_page, startPage + maxVisible - 1);
        
        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }
        
        for (let p = startPage; p <= endPage; p++) {
            const activeClass = p === data.current_page 
                ? 'bg-emerald-700 text-white font-semibold focus:z-20' 
                : 'text-gray-900 bg-white hover:bg-gray-50 focus:z-20';
            pageButtons += `
                <button type="button" onclick="previewStudentData(${p})" 
                   class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:outline-offset-0 ${activeClass}">
                   ${p}
                </button>
            `;
        }

        return `
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-4 flex-shrink-0 rounded-lg shadow-sm">
                <!-- Mobile view -->
                <div class="flex flex-1 justify-between items-center sm:hidden w-full">
                    <button type="button" onclick="previewStudentData(${data.current_page - 1})" ${prevDisabled}
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 select-none">
                        Previous
                    </button>
                    <span class="text-sm text-gray-700 font-medium">Page ${data.current_page} of ${data.last_page}</span>
                    <button type="button" onclick="previewStudentData(${data.current_page + 1})" ${nextDisabled}
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 select-none">
                        Next
                    </button>
                </div>
                
                <!-- Desktop view -->
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-semibold text-emerald-800">${data.from ?? 0}</span> to <span class="font-semibold text-emerald-800">${data.to ?? 0}</span> of <span class="font-semibold text-emerald-800">${data.total}</span> records
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
                            <button type="button" onclick="previewStudentData(${data.current_page - 1})" ${prevDisabled}
                                class="relative inline-flex items-center rounded-l-md px-3 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 select-none">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            ${pageButtons}
                            <button type="button" onclick="previewStudentData(${data.current_page + 1})" ${nextDisabled}
                                class="relative inline-flex items-center rounded-r-md px-3 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 select-none">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        `;
    }

    // Preview Student Data
    function previewStudentData(page = 1) {
        const course = document.querySelector('select[name="course"]').value;
        const year = document.getElementById('student_year').value;
        const previewContainer = document.getElementById('preview_container');
        
        // Prepare loading state
        previewContainer.innerHTML = `
            <div class="flex items-center justify-center h-full p-12">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-emerald-800 mb-4"></div>
                    <p class="text-gray-500 font-semibold">Loading student data...</p>
                </div>
            </div>
        `;
        
        // Fetch data
        fetch(`{{ route('admin.reports.student-preview') }}?course=${encodeURIComponent(course)}&year=${encodeURIComponent(year)}&page=${page}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load student preview');
                return response.json();
            })
            .then(data => {
                if (!data.data || data.data.length === 0) {
                    previewContainer.innerHTML = `
                        <div class="flex items-center justify-center h-full p-12">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="mt-4 text-gray-500 font-semibold">No students found matching your filters.</p>
                                <button type="button" onclick="resetPreview()" class="mt-4 text-emerald-800 font-bold hover:underline">Back to Selection</button>
                            </div>
                        </div>
                    `;
                    return;
                }
                
                let rows = data.data.map((student, index) => {
                    const rowNumber = (data.from || 1) + index;
                    return `
                        <tr class="border-b border-gray-200 hover:bg-gray-100 transition ${rowNumber % 2 == 0 ? 'bg-white' : 'bg-gray-50'}">
                            <td class="py-3 px-4 font-semibold text-gray-600">${rowNumber}</td>
                            <td class="py-3 px-4 font-medium text-gray-800">${student.sid}</td>
                            <td class="py-3 px-4 text-gray-700">${student.fullname}</td>
                            <td class="py-3 px-4">
                                <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold">${student.course}</span>
                            </td>
                            <td class="py-3 px-4 text-center">${student.year}</td>
                            <td class="py-3 px-4 text-right text-gray-500 text-xs">${student.campus}</td>
                        </tr>
                    `;
                }).join('');

                const paginationHtml = renderPagination(data);

                previewContainer.innerHTML = `
                    <div class="p-6 h-full flex flex-col">
                        <div class="flex items-center justify-between mb-4 flex-shrink-0">
                            <h3 class="text-lg font-bold text-gray-800">Student Preview (${data.total} records)</h3>
                            <button type="button" onclick="resetPreview()" class="text-sm text-emerald-800 font-bold hover:underline flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 8.959 8.959 0 01-9 9m-9-9a9 9 0 019-9" />
                                </svg>
                                Back
                            </button>
                        </div>
                        
                        <div class="overflow-y-auto flex-grow shadow border-b border-gray-200 sm:rounded-lg custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-emerald-700 text-white text-sm sticky top-0 z-10">
                                    <tr>
                                        <th class="py-3 px-4">#</th>
                                        <th class="py-3 px-4">Student ID</th>
                                        <th class="py-3 px-4">Full Name</th>
                                        <th class="py-3 px-4">Course</th>
                                        <th class="py-3 px-4 text-center">Year</th>
                                        <th class="py-3 px-4 text-right">Campus</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    ${rows}
                                </tbody>
                            </table>
                        </div>
                        
                        ${paginationHtml}
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error fetching student preview:', error);
                previewContainer.innerHTML = `
                    <div class="flex items-center justify-center h-full p-12">
                        <div class="text-center text-red-500">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-4 font-bold">Failed to load student preview.</p>
                            <button onclick="resetPreview()" class="mt-4 text-emerald-800 font-bold hover:underline">Try Again</button>
                        </div>
                    </div>
                `;
            });
    }
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #064e3b;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #065f46;
    }
</style>
@endsection
