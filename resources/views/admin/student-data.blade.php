@extends('layouts.app')

@section('title', 'Student Data')
@section('header', 'Student Data')

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
        <h3 class="text-gray-700 font-bold text-lg">Master List of Students</h3>
        
        <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <form method="GET" action="{{ route('admin.student-data') }}">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full py-2 pl-10 pr-4 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600 focus:ring-1 focus:ring-green-600 transition" 
                        placeholder="Search students...">
                </form>
            </div>
            
            <button onclick="showAddModal()" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Student
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <form id="filterForm" method="GET" action="{{ route('admin.student-data') }}">
            <!-- Keep existing global search -->
            <input type="hidden" name="search" value="{{ request('search') }}">

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'sid', 'direction' => request('sort') == 'sid' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Student ID
                                @include('partials.sort-icon', ['field' => 'sid'])
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
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'middlename', 'direction' => request('sort') == 'middlename' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Middle Name
                                @include('partials.sort-icon', ['field' => 'middlename'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'lastname', 'direction' => request('sort') == 'lastname' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Last Name
                                @include('partials.sort-icon', ['field' => 'lastname'])
                            </a>
                        </th>

                        @if (session('location') != 'DCC BED')
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'department', 'direction' => request('sort') == 'department' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Department
                                    @include('partials.sort-icon', ['field' => 'department'])
                                </a>
                            </th>
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'course', 'direction' => request('sort') == 'course' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Course
                                    @include('partials.sort-icon', ['field' => 'course'])
                                </a>
                            </th>
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'year', 'direction' => request('sort') == 'year' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Year
                                    @include('partials.sort-icon', ['field' => 'year'])
                                </a>
                            </th>
                        @endif

                        @if(session('location') == 'DCC BED')
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'grade', 'direction' => request('sort') == 'grade' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Grade
                                @include('partials.sort-icon', ['field' => 'grade'])
                            </a>
                        </th>
                        <th class="py-3 px-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'section', 'direction' => request('sort') == 'section' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                Section
                                @include('partials.sort-icon', ['field' => 'section'])
                            </a>
                        </th>
                        @endif

                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                    <!-- Filter Row -->
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <td class="py-2 px-6">
                            <input type="text" name="sid" value="{{ request('sid') }}" placeholder="ID" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                        </td>
                        <td class="py-2 px-6">
                            <input type="text" name="rfid" value="{{ request('rfid') }}" placeholder="RFID" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                        </td>
                        <td class="py-2 px-6">
                            <input type="text" name="firstname" value="{{ request('firstname') }}" placeholder="First" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                        </td>
                        <td class="py-2 px-6">
                            <input type="text" name="middlename" value="{{ request('middlename') }}" placeholder="Middle" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                        </td>
                        <td class="py-2 px-6">
                            <input type="text" name="lastname" value="{{ request('lastname') }}" placeholder="Last" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                        </td>
                        
                        @if (session('location') != 'DCC BED')
                            <td class="py-2 px-6">
                                <select name="department" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                                    <option value="">All</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d }}" {{ request('department') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-2 px-6">
                                <input type="text" name="course" value="{{ request('course') }}" placeholder="Course" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                            <td class="py-2 px-6">
                                <select name="year" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                                    <option value="">Year</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </td>
                        @endif

                        @if(session('location') == 'DCC BED')
                            <td class="py-2 px-6">
                                <select name="grade" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                                    <option value="">Grade</option>
                                    @foreach($grades as $g)
                                        <option value="{{ $g }}" {{ request('grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-2 px-6">
                                <input type="text" name="section" value="{{ request('section') }}" placeholder="Section" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                        @endif

                        <td class="py-2 px-6 text-center">
                            <button type="submit" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition">Filter</button>
                        </td>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($students as $student)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="py-4 px-6 font-medium text-gray-800">
                            {{ $student->sid }}
                        </td>
                        <td class="py-4 px-6 text-gray-700">
                            {{ $student->rfid ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $student->firstname }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $student->middlename ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $student->lastname }}
                        </td>

                        @if(session('location') != 'DCC BED')
                            <td class="py-4 px-6 text-gray-700">
                                {{ $student->department }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-semibold">
                                    {{ $student->course }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                {{ $student->year }}
                            </td>
                        @endif

                        @if(session('location') == 'DCC BED')
                        <td class="py-4 px-6">
                            {{ $student->grade }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $student->section }}
                        </td>
                        @endif

                        <td class="py-4 px-6 text-center">
                            <div class="flex item-center justify-center gap-3">
                                <button type="button" onclick="showEditModal({{ $student->toJson() }})" class="text-orange-600 hover:text-orange-800 transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form action="{{ route('admin.student-data.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?')">
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
                        <td colspan="{{ session('location') == 'DCC BED' ? 8 : 9 }}" class="py-4 px-6 text-center text-gray-400 italic">
                            No students found in the records.
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
            Showing <span class="font-semibold text-gray-700">{{ $students->firstItem() ?? 0 }}</span> 
            to <span class="font-semibold text-gray-700">{{ $students->lastItem() ?? 0 }}</span> 
            of <span class="font-semibold text-gray-700">{{ $students->total() }}</span> students
        </div>
        <div class="pagination-container">
            {{ $students->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAddModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.student-data.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Student</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Student ID</label>
                            <input type="text" name="sid" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">RFID</label>
                            <input type="text" name="rfid" id="add_rfid" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" name="firstname" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                                <input type="text" name="middlename" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" name="lastname" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                            </div>
                        </div>
                        @if(session('location') == 'DCC BED')
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Grade</label>
                                <select name="grade" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                                    <option value="">Select Grade</option>
                                    @foreach(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'] as $g)
                                        <option value="{{ $g }}">{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Section</label>
                                <input type="text" name="section" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Department</label>
                                <select name="department" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                                    <option value="">Select Department</option>
                                    <option value="CJE">CJE</option>
                                    <option value="HME">HME</option>
                                    <option value="ITE">ITE</option>
                                    <option value="CBA">CBA</option>
                                    <option value="CELA">CELA</option>
                                </select>
                            </div>
                            
                            <div id="course_container">
                                <label class="block text-sm font-medium text-gray-700">Course</label>
                                
                                <!-- Default Text Input -->
                                <input type="text" name="course" id="course_text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2 course-input">
                                
                                <!-- CBA Dropdown -->
                                <select name="course" id="course_cba" disabled class="hidden mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2 course-input">
                                    <option value="">Select Course</option>
                                    <option value="FM">FM</option>
                                    <option value="MM">MM</option>
                                    <option value="HRDM">HRDM</option>
                                </select>

                                <!-- CELA Dropdown -->
                                <select name="course" id="course_cela" disabled class="hidden mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2 course-input">
                                    <option value="">Select Course</option>
                                    <option value="BEED-Generalist">BEED-Generalist</option>
                                    <option value="BSNED">BSNED</option>
                                    <option value="BTLE-HE">BTLE-HE</option>
                                    <option value="BSED-English">BSED-English</option>
                                    <option value="BSED-Math">BSED-Math</option>
                                    <option value="BSED-Science">BSED-Science</option>
                                    <option value="BSED-Social Studies">BSED-Social Studies</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Year</label>
                                <select name="year" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-700 text-base font-medium text-white hover:bg-emerald-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Save Student</button>
                    <button type="button" onclick="closeAddModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Student</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="edit_sid">Student ID</label>
                            <input type="text" name="sid" id="edit_sid" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="edit_rfid">RFID</label>
                            <input type="text" name="rfid" id="edit_rfid" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="edit_firstname">First Name</label>
                                <input type="text" name="firstname" id="edit_firstname" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="edit_middlename">Middle Name</label>
                                <input type="text" name="middlename" id="edit_middlename" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="edit_lastname">Last Name</label>
                                <input type="text" name="lastname" id="edit_lastname" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                            </div>
                        </div>
                        @if(session('location') == 'DCC BED')
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="edit_grade">Grade</label>
                                <select name="grade" id="edit_grade" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                                    <option value="">Select Grade</option>
                                    @foreach(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'] as $g)
                                        <option value="{{ $g }}">{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="edit_section">Section</label>
                                <input type="text" name="section" id="edit_section" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="edit_department">Department</label>
                                <select name="department" id="edit_department" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                                    <option value="">Select Department</option>
                                    <option value="CJE">CJE</option>
                                    <option value="HME">HME</option>
                                    <option value="ITE">ITE</option>
                                    <option value="CBA">CBA</option>
                                    <option value="CELA">CELA</option>
                                </select>
                            </div>
                            
                            <div id="edit_course_container">
                                <label class="block text-sm font-medium text-gray-700" for="edit_course_text">Course</label>
                                <input type="text" name="course" id="edit_course_text" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2 course-input">
                                
                                <select name="course" id="edit_course_cba" disabled class="hidden mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2 course-input">
                                    <option value="">Select Course</option>
                                    <option value="FM">FM</option>
                                    <option value="MM">MM</option>
                                    <option value="HRDM">HRDM</option>
                                </select>

                                <select name="course" id="edit_course_cela" disabled class="hidden mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2 course-input">
                                    <option value="">Select Course</option>
                                    <option value="BEED-Generalist">BEED-Generalist</option>
                                    <option value="BSNED">BSNED</option>
                                    <option value="BTLE-HE">BTLE-HE</option>
                                    <option value="BSED-English">BSED-English</option>
                                    <option value="BSED-Math">BSED-Math</option>
                                    <option value="BSED-Science">BSED-Science</option>
                                    <option value="BSED-Social Studies">BSED-Social Studies</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="edit_year">Year</label>
                                <select name="year" id="edit_year" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm border p-2">
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-700 text-base font-medium text-white hover:bg-emerald-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Update Student</button>
                    <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add Student Modal Logic
        const deptSelect = document.querySelector('select[name="department"]');
        if (deptSelect) {
            const courseContainer = document.getElementById('course_container');
            const courseText = document.getElementById('course_text');
            const courseCba = document.getElementById('course_cba');
            const courseCela = document.getElementById('course_cela');
            const allCourseInputs = [courseText, courseCba, courseCela];
    
            deptSelect.addEventListener('change', function() {
                const dept = this.value;
    
                // Reset all first
                allCourseInputs.forEach(input => {
                    input.classList.add('hidden');
                    input.disabled = true;
                    input.required = false; 
                });
                courseContainer.classList.add('hidden'); // default hidden

                if (dept === 'CBA') {
                    courseContainer.classList.remove('hidden');
                    courseCba.classList.remove('hidden');
                    courseCba.disabled = false;
                    courseCba.required = true;
                } else if (dept === 'CELA') {
                    courseContainer.classList.remove('hidden');
                    courseCela.classList.remove('hidden');
                    courseCela.disabled = false;
                    courseCela.required = true;
                } else if (dept) {
                    // For other departments, populate Hidden Course Input but keep container hidden
                    // Use courseText as the carrier
                    courseText.value = dept; 
                    courseText.disabled = false; // Enabled so it submits
                }
            });
        }

        // Edit Student Modal Logic
        const editDeptSelect = document.getElementById('edit_department');
        if (editDeptSelect) {
            const editCourseContainer = document.getElementById('edit_course_container');
            const editCourseText = document.getElementById('edit_course_text');
            const editCourseCba = document.getElementById('edit_course_cba');
            const editCourseCela = document.getElementById('edit_course_cela');
            const allEditCourseInputs = [editCourseText, editCourseCba, editCourseCela];

            editDeptSelect.addEventListener('change', function() {
                const dept = this.value;

                // Reset all first
                allEditCourseInputs.forEach(input => {
                    input.classList.add('hidden');
                    input.disabled = true;
                    input.required = false; 
                });
                editCourseContainer.classList.add('hidden');

                if (dept === 'CBA') {
                    editCourseContainer.classList.remove('hidden');
                    editCourseCba.classList.remove('hidden');
                    editCourseCba.disabled = false;
                    editCourseCba.required = true;
                } else if (dept === 'CELA') {
                    editCourseContainer.classList.remove('hidden');
                    editCourseCela.classList.remove('hidden');
                    editCourseCela.disabled = false;
                    editCourseCela.required = true;
                } else if (dept) {
                    editCourseText.value = dept; 
                    editCourseText.disabled = false;
                }
            });
        }
    });

    // Prevent RFID scanner (Enter) from submitting forms
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && (e.target.id === 'add_rfid' || e.target.id === 'edit_rfid')) {
            e.preventDefault();
        }
    });

    function showAddModal() {
        document.getElementById('addStudentModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addStudentModal').classList.add('hidden');
    }
    function showEditModal(student) {
        const updateUrl = "{{ route('admin.student-data.update', ':id') }}";
        document.getElementById('editForm').action = updateUrl.replace(':id', student.id);
        document.getElementById('edit_sid').value = student.sid;
        document.getElementById('edit_rfid').value = student.rfid || '';
        document.getElementById('edit_firstname').value = student.firstname;
        document.getElementById('edit_middlename').value = student.middlename || '';
        document.getElementById('edit_lastname').value = student.lastname;
        
        if (document.getElementById('edit_department')) {
            const editDeptSelect = document.getElementById('edit_department');
            editDeptSelect.value = student.department;
            
            // Trigger change event to set up correct course inputs
            editDeptSelect.dispatchEvent(new Event('change'));

            // Populate the correct course input
            if (student.department === 'CBA') {
                document.getElementById('edit_course_cba').value = student.course;
            } else if (student.department === 'CELA') {
                document.getElementById('edit_course_cela').value = student.course;
            } else {
                 document.getElementById('edit_course_text').value = student.course;
            }
        }
        
        if (document.getElementById('edit_year')) document.getElementById('edit_year').value = student.year;
        
        if (document.getElementById('edit_grade')) document.getElementById('edit_grade').value = student.grade;
        if (document.getElementById('edit_section')) document.getElementById('edit_section').value = student.section;

        document.getElementById('editStudentModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editStudentModal').classList.add('hidden');
    }
</script>
@endsection
