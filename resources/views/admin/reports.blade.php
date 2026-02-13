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
                <label class="block text-gray-600 text-sm font-semibold mb-2">Search Students (Optional)</label>
                <input type="text" name="search" placeholder="Name or Student ID" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
            </div>
             <div class="mb-4">
                <label class="block text-gray-600 text-sm font-semibold mb-2">Date Range</label>
                <div class="flex space-x-2">
                    <input type="date" name="start_date" class="w-1/2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                    <input type="date" name="end_date" class="w-1/2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-green-600">
                </div>
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
    
    <div id="preview_container" class="card flex items-center justify-center bg-gray-50 border-dashed border-2 border-gray-300 overflow-hidden relative min-h-[300px]">
             <div class="text-center text-gray-400 transition-all duration-500" id="preview_content">
                 <img src="{{ asset('images/pdf_export.png') }}" alt="PDF Format" class="max-w-[200px] h-auto drop-shadow-2xl animate-bounce-slow">
                 <p class="mt-4 font-semibold text-gray-500">PDF Selection Preview</p>
             </div>
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
</script>
@endsection
