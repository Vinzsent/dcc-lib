@extends('layouts.app')

@section('title', 'Employee Data')
@section('header', 'Employee Data')

@section('content')
<!-- Alerts -->
@if(session('success'))
<div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 flex justify-between items-center transition-all duration-500" id="success-alert">
    <span>{{ session('success') }}</span>
    <button onclick="document.getElementById('success-alert').style.display='none'" class="text-green-700 font-bold">&times;</button>
</div>
@endif

@if($errors->any())
<div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 flex justify-between items-center">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button onclick="this.parentElement.style.display='none'" class="text-red-700 font-bold">&times;</button>
</div>
@endif

<div class="card">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h3 class="text-gray-700 font-bold text-lg">Master List of Employees</h3>
        
        <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <form method="GET" action="{{ route('admin.employee-data') }}">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full py-2 pl-10 pr-4 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600 focus:ring-1 focus:ring-green-600 transition" 
                        placeholder="Search employees...">
                </form>
            </div>
            
            <button onclick="showAddModal()" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Employee
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <form id="filterForm" method="GET" action="{{ route('admin.employee-data') }}">
            <!-- Keep existing global search -->
            <input type="hidden" name="search" value="{{ request('search') }}">

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('sort') == 'id' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Employee ID
                                @include('partials.sort-icon', ['field' => 'id'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'rfid', 'direction' => request('sort') == 'rfid' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                RFID
                                @include('partials.sort-icon', ['field' => 'rfid'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'firstname', 'direction' => request('sort') == 'firstname' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                First Name
                                @include('partials.sort-icon', ['field' => 'firstname'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'lastname', 'direction' => request('sort') == 'lastname' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Last Name
                                @include('partials.sort-icon', ['field' => 'lastname'])
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
                                Employment Type
                                @include('partials.sort-icon', ['field' => 'employment_type'])
                            </a>
                        </th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                    <!-- Filter Row -->
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <td class="py-2 px-6">
                            <input type="text" name="id" value="{{ request('id') }}" placeholder="ID" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                        </td>
                        <td class="py-2 px-6">
                            <input type="text" name="rfid" value="{{ request('rfid') }}" placeholder="RFID" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                        </td>
                        <td class="py-2 px-6">
                            <input type="text" name="firstname" value="{{ request('firstname') }}" placeholder="First" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                        </td>
                        <td class="py-2 px-6">
                            <input type="text" name="lastname" value="{{ request('lastname') }}" placeholder="Last" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
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
                        <td class="py-2 px-6 text-center">
                            <button type="submit" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition">Filter</button>
                        </td>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($employees as $employee)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="py-4 px-6 font-medium text-gray-800">
                            {{ $employee->id }}
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ $employee->rfid ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $employee->firstname }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $employee->lastname }}
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ $employee->department ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-semibold">
                                {{ $employee->position ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            {{ $employee->employment_type ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex item-center justify-center gap-3">
                                <button type="button" onclick="showEditModal(this)" data-employee="{{ $employee->toJson() }}" class="text-orange-600 hover:text-orange-800 transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button type="button" onclick="deleteEmployee('{{ route('admin.employee-data.destroy', $employee->id) }}')" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-4 px-6 text-center text-gray-400 italic">
                            No employees found in the records.
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
            Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} employees
        </div>
        <div class="pagination-container">
            {{ $employees->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Backdrop with glassmorphism / slight blur -->
        <div class="fixed inset-0 bg-slate-900 bg-opacity-60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="hideAddModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" onclick="hideAddModal()" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('admin.employee-data.store') }}" method="POST" class="p-6 sm:p-8">
                @csrf
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Add New Employee</h3>
                    <p class="text-sm text-gray-500 mt-1">Please fill in the employee details below.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">RFID</label>
                        <input type="text" name="rfid" placeholder="Scan or enter RFID number" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">First Name *</label>
                        <input type="text" name="firstname" required placeholder="John" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name *</label>
                        <input type="text" name="lastname" required placeholder="Doe" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Middle Name</label>
                        <input type="text" name="middlename" placeholder="Smith" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Department</label>
                        <input type="text" name="department" placeholder="e.g., College Department" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Position</label>
                        <input type="text" name="position" placeholder="e.g., Instructor" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Employment Type</label>
                        <select name="employment_type" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                            <option value="">Select Employment Type</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-150">
                    <button type="button" onclick="hideAddModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-lg text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-lg text-sm font-semibold bg-emerald-700 hover:bg-emerald-800 text-white shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Add Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Backdrop with glassmorphism / slight blur -->
        <div class="fixed inset-0 bg-slate-900 bg-opacity-60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="hideEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" onclick="hideEditModal()" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="editForm" method="POST" class="p-6 sm:p-8">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Edit Employee</h3>
                    <p class="text-sm text-gray-500 mt-1">Update the employee information below.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">RFID</label>
                        <input type="text" name="rfid" id="edit_rfid" placeholder="Scan or enter RFID number" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">First Name *</label>
                        <input type="text" name="firstname" id="edit_firstname" required placeholder="John" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name *</label>
                        <input type="text" name="lastname" id="edit_lastname" required placeholder="Doe" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Middle Name</label>
                        <input type="text" name="middlename" id="edit_middlename" placeholder="Smith" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Department</label>
                        <input type="text" name="department" id="edit_department" placeholder="e.g., College Department" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Position</label>
                        <input type="text" name="position" id="edit_position" placeholder="e.g., Instructor" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Employment Type</label>
                        <select name="employment_type" id="edit_employment_type" class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition duration-200">
                            <option value="">Select Employment Type</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-150">
                    <button type="button" onclick="hideEditModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-lg text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-lg text-sm font-semibold bg-emerald-700 hover:bg-emerald-800 text-white shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Global Hidden Delete Form -->
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <!-- Backdrop with glassmorphism / slight blur -->
        <div class="fixed inset-0 bg-slate-900 bg-opacity-60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="hideDeleteModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="p-6 sm:p-8">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 text-red-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-grow border-0">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Delete Employee</h3>
                        <p class="text-sm text-gray-500">Are you sure you want to delete this employee? This action cannot be undone and the record will be permanently removed.</p>
                    </div>
                </div>
                
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
                    <button type="button" onclick="hideDeleteModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-lg text-sm font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button type="button" onclick="submitDelete()" class="w-full sm:w-auto px-5 py-2.5 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let deleteUrl = '';

    function showAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function showEditModal(button) {
        const employee = JSON.parse(button.getAttribute('data-employee'));
        const updateUrl = "{{ route('admin.employee-data.update', ':id') }}";
        document.getElementById('editForm').action = updateUrl.replace(':id', employee.id);
        document.getElementById('edit_rfid').value = employee.rfid || '';
        document.getElementById('edit_firstname').value = employee.firstname || '';
        document.getElementById('edit_middlename').value = employee.middlename || '';
        document.getElementById('edit_lastname').value = employee.lastname || '';
        document.getElementById('edit_department').value = employee.department || '';
        document.getElementById('edit_position').value = employee.position || '';
        document.getElementById('edit_employment_type').value = employee.employment_type || '';
        
        document.getElementById('editModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function deleteEmployee(url) {
        deleteUrl = url;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = '';
        deleteUrl = '';
    }

    function submitDelete() {
        if (deleteUrl) {
            const form = document.getElementById('deleteForm');
            form.action = deleteUrl;
            form.submit();
        }
    }

    // Auto-hide success alert after 5 seconds
    setTimeout(() => {
        const alert = document.getElementById('success-alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 5000);
</script>
@endsection
