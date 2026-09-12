@extends('layouts.app')

@section('title', 'Book Type Management')
@section('header', 'Book Type & Section Management')

@section('content')
<div class="space-y-6">

    {{-- Top Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 p-4 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 14c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="text-sm font-semibold">Please correct the following errors:</h4>
                    <ul class="list-disc list-inside text-xs mt-1 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100 text-gray-700 font-bold flex-shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Total Types</p>
                <p class="text-xl font-bold text-gray-800">{{ $totalCount }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-purple-100 text-purple-800 font-bold flex-shrink-0">
                <svg class="w-5 h-5 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">College</p>
                <p class="text-xl font-bold text-purple-700">{{ $collegeCount }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-100 text-amber-800 font-bold flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Elementary</p>
                <p class="text-xl font-bold text-amber-700">{{ $elemCount }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-100 text-blue-800 font-bold flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">HS / SHS</p>
                <p class="text-xl font-bold text-blue-700">{{ $hsCount }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-emerald-100 text-emerald-800 font-bold flex-shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Active Status</p>
                <p class="text-xl font-bold text-emerald-700">{{ $activeCount }}</p>
            </div>
        </div>
    </div>

    {{-- Main Container Card with Navigation Tabs --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Tabs Navigation Bar --}}
        <div class="border-b border-gray-200 bg-gray-50/70 px-4 pt-3 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap space-x-1 sm:space-x-2">
                {{-- Tab: All --}}
                <a href="{{ route('admin.library.book-types.index', ['tab' => 'all', 'status' => request('status'), 'search' => request('search')]) }}"
                   class="px-3.5 py-2.5 text-sm font-semibold rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $tab === 'all' ? 'border-emerald-700 bg-white text-emerald-800 shadow-xs' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>All Book Types</span>
                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ $tab === 'all' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">{{ $totalCount }}</span>
                </a>

                {{-- Tab: College --}}
                <a href="{{ route('admin.library.book-types.index', ['tab' => 'college', 'status' => request('status'), 'search' => request('search')]) }}"
                   class="px-3.5 py-2.5 text-sm font-semibold rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $tab === 'college' ? 'border-purple-600 bg-white text-purple-800 shadow-xs' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                    <span>College</span>
                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ $tab === 'college' ? 'bg-purple-100 text-purple-800' : 'bg-gray-200 text-gray-600' }}">{{ $collegeCount }}</span>
                </a>

                {{-- Tab: Elementary --}}
                <a href="{{ route('admin.library.book-types.index', ['tab' => 'elementary', 'status' => request('status'), 'search' => request('search')]) }}"
                   class="px-3.5 py-2.5 text-sm font-semibold rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $tab === 'elementary' ? 'border-amber-600 bg-white text-amber-800 shadow-xs' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Elementary</span>
                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ $tab === 'elementary' ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-600' }}">{{ $elemCount }}</span>
                </a>

                {{-- Tab: High School / Senior High School --}}
                <a href="{{ route('admin.library.book-types.index', ['tab' => 'highschool', 'status' => request('status'), 'search' => request('search')]) }}"
                   class="px-3.5 py-2.5 text-sm font-semibold rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $tab === 'highschool' ? 'border-blue-600 bg-white text-blue-800 shadow-xs' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                    <span>High School / Senior High</span>
                    <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ $tab === 'highschool' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-600' }}">{{ $hsCount }}</span>
                </a>
            </div>

            <div class="pb-2">
                <button type="button" onclick="openCreateModal('{{ $tab }}')" class="inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-emerald-800 hover:bg-emerald-900 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Book Type
                </button>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="p-4 bg-white border-b border-gray-100">
            <form method="GET" action="{{ route('admin.library.book-types.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or description..."
                           class="w-full pl-9 pr-8 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                    @if(request('search'))
                        <a href="{{ route('admin.library.book-types.index', ['tab' => $tab, 'status' => request('status')]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </a>
                    @endif
                </div>

                <div class="w-full sm:w-48">
                    <select name="status" onchange="this.form.submit()" class="w-full py-2 px-3 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                        <option value="">All Statuses</option>
                        <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active Only</option>
                        <option value="INACTIVE" {{ request('status') === 'INACTIVE' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                </div>

                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition">
                    Filter
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3 px-6 font-semibold">ID</th>
                        <th class="py-3 px-6 font-semibold">Type / Section Name</th>
                        <th class="py-3 px-6 font-semibold">Level / Category</th>
                        <th class="py-3 px-6 font-semibold">Description</th>
                        <th class="py-3 px-6 font-semibold text-center">Status</th>
                        <th class="py-3 px-6 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($bookTypes as $type)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-4 px-6 text-gray-400 font-mono text-xs">{{ $type->id }}</td>
                            <td class="py-4 px-6 font-semibold text-gray-900">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $type->status === 'ACTIVE' ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                                    <span>{{ $type->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if($type->level === 'College')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                        College
                                    </span>
                                @elseif($type->level === 'Elementary')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        Elementary
                                    </span>
                                @elseif($type->level === 'High School / Senior High School')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                        HS / Senior High
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                        All Levels
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-gray-500 max-w-sm">
                                {{ $type->description ?: '—' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($type->status === 'ACTIVE')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="openEditModal({{ json_encode($type) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Book Type">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <button type="button" onclick="openDeleteModal('{{ route('admin.library.book-types.destroy', $type->id) }}', '{{ addslashes($type->name) }}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete Book Type">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-gray-400">
                                <svg class="h-12 w-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-sm font-medium text-gray-600">No book types found in this tab.</p>
                                <p class="text-xs text-gray-400 mt-1">Click "Add Book Type" above to configure sections for this category.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($bookTypes) && $bookTypes->hasPages())
            <div class="p-4 border-t border-gray-100 pagination-container">
                {{ $bookTypes->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ================= MODALS ================= --}}

{{-- Create Modal --}}
<div id="createModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeCreateModal()"></div>
        
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-gray-100">
            <div class="bg-emerald-800 px-6 py-4 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <h3 class="text-base font-bold">Add New Book Type</h3>
                </div>
                <button type="button" onclick="closeCreateModal()" class="text-emerald-200 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('admin.library.book-types.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Target Level / Category <span class="text-red-500">*</span></label>
                        <select id="create_level" name="level" required class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 bg-white">
                            <option value="All">All Levels (Universal)</option>
                            <option value="College">College</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School / Senior High School">High School / Senior High School</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Book Type / Section Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g., General Reference, Fiction, Textbook" class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Description</label>
                        <textarea name="description" rows="3" placeholder="Brief description of materials in this category..." class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 bg-white">
                            <option value="ACTIVE" selected>Active (Available in Borrow Selection)</option>
                            <option value="INACTIVE">Inactive (Hidden from Borrow Selection)</option>
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3.5 flex items-center justify-end gap-2 border-t border-gray-100">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-800 hover:bg-emerald-900 rounded-lg shadow-sm transition">Save Book Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeEditModal()"></div>
        
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-gray-100">
            <div class="bg-emerald-800 px-6 py-4 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <h3 class="text-base font-bold">Edit Book Type</h3>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-emerald-200 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Target Level / Category <span class="text-red-500">*</span></label>
                        <select id="edit_level" name="level" required class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 bg-white">
                            <option value="All">All Levels (Universal)</option>
                            <option value="College">College</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School / Senior High School">High School / Senior High School</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Book Type / Section Name <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_name" name="name" required class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Description</label>
                        <textarea id="edit_description" name="description" rows="3" class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status <span class="text-red-500">*</span></label>
                        <select id="edit_status" name="status" required class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 bg-white">
                            <option value="ACTIVE">Active (Available in Borrow Selection)</option>
                            <option value="INACTIVE">Inactive (Hidden from Borrow Selection)</option>
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3.5 flex items-center justify-end gap-2 border-t border-gray-100">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-800 hover:bg-emerald-900 rounded-lg shadow-sm transition">Update Book Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeDeleteModal()"></div>
        
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-100">
            <div class="p-6">
                <div class="flex items-center gap-3 text-red-600 mb-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 14c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Delete Book Type</h3>
                </div>
                <p class="text-sm text-gray-600">
                    Are you sure you want to delete <span id="deleteTypeName" class="font-bold text-gray-900"></span>?
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    Note: If this book type has existing borrow records, deletion will be blocked to maintain history integrity.
                </p>
            </div>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="bg-gray-50 px-6 py-3.5 flex items-center justify-end gap-2 border-t border-gray-100">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm transition">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCreateModal(tab) {
        const levelSelect = document.getElementById('create_level');
        if (tab === 'college') {
            levelSelect.value = 'College';
        } else if (tab === 'elementary') {
            levelSelect.value = 'Elementary';
        } else if (tab === 'highschool') {
            levelSelect.value = 'High School / Senior High School';
        } else {
            levelSelect.value = 'All';
        }
        document.getElementById('createModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openEditModal(type) {
        document.getElementById('editForm').action = `/admin/library/book-types/${type.id}`;
        document.getElementById('edit_name').value = type.name || '';
        document.getElementById('edit_level').value = type.level || 'All';
        document.getElementById('edit_description').value = type.description || '';
        document.getElementById('edit_status').value = type.status || 'ACTIVE';
        document.getElementById('editModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openDeleteModal(url, name) {
        document.getElementById('deleteForm').action = url;
        document.getElementById('deleteTypeName').textContent = `"${name}"`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeDeleteModal();
        }
    });
</script>
@endsection