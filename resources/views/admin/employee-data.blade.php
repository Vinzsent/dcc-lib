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
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'eid', 'direction' => request('sort') == 'eid' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Employee ID
                                @include('partials.sort-icon', ['field' => 'eid'])
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
                            <input type="text" name="eid" value="{{ request('eid') }}" placeholder="ID" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
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
                            {{ $employee->eid }}
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
                                <button onclick="showEditModal({{ $employee->toJson() }})" class="text-orange-600 hover:text-orange-800 transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form action="{{ route('admin.employee-data.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
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
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="hideAddModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.employee-data.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add New Employee</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee ID *</label>
                            <input type="text" name="eid" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">RFID</label>
                            <input type="text" name="rfid" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name *</label>
                            <input type="text" name="firstname" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input type="text" name="middlename" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name *</label>
                            <input type="text" name="lastname" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <input type="text" name="department" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Position</label>
                            <input type="text" name="position" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employment Type</label>
                            <input type="text" name="employment_type" placeholder="e.g., Full-time, Part-time, Contract" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-700 text-base font-medium text-white hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add Employee
                    </button>
                    <button type="button" onclick="hideAddModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="hideEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Employee</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee ID *</label>
                            <input type="text" name="eid" id="edit_eid" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">RFID</label>
                            <input type="text" name="rfid" id="edit_rfid" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name *</label>
                            <input type="text" name="firstname" id="edit_firstname" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input type="text" name="middlename" id="edit_middlename" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name *</label>
                            <input type="text" name="lastname" id="edit_lastname" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <input type="text" name="department" id="edit_department" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Position</label>
                            <input type="text" name="position" id="edit_position" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employment Type</label>
                            <input type="text" name="employment_type" id="edit_employment_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-700 text-base font-medium text-white hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Employee
                    </button>
                    <button type="button" onclick="hideEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function showEditModal(employee) {
        document.getElementById('editForm').action = `/admin/employee-data/${employee.id}`;
        document.getElementById('edit_eid').value = employee.eid || '';
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

    // Auto-hide success alert after 5 seconds
    setTimeout(() => {
        const alert = document.getElementById('success-alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 5000);
</script>
@endsection
