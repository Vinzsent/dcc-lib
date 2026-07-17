@extends('layouts.app')

@section('title', 'Research Database')
@section('header', 'Library - Research Database')

@section('content')
    <!-- Alerts -->
    <div id="alert-container"></div>

    <div class="card bg-white p-6 rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-gray-700 font-bold text-lg">Master List of Research</h3>

            <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                <div class="relative w-full md:w-80">
                    <form method="GET" action="{{ route('admin.library.research.index') }}">
                        @foreach(request()->except('search', 'page') as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="w-full py-2 pl-10 {{ request('search') ? 'pr-10' : 'pr-4' }} text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600 focus:ring-1 focus:ring-green-600 transition"
                            placeholder="Search research...">
                        @if(request('search'))
                            <a href="{{ route('admin.library.research.index', request()->except('search', 'page')) }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </form>
                </div>

                <button onclick="showAddModal()"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Add Research
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <form id="filterForm" method="GET" action="{{ route('admin.library.research.index') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="direction" value="{{ request('direction') }}">

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'accession_no', 'direction' => request('sort') == 'accession_no' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Accession No
                                    @include('partials.sort-icon', ['field' => 'accession_no'])
                                </a>
                            </th>
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'barcode', 'direction' => request('sort') == 'barcode' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Barcode
                                    @include('partials.sort-icon', ['field' => 'barcode'])
                                </a>
                            </th>
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'direction' => request('sort') == 'title' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Title
                                    @include('partials.sort-icon', ['field' => 'title'])
                                </a>
                            </th>
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'author', 'direction' => request('sort') == 'author' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Author
                                    @include('partials.sort-icon', ['field' => 'author'])
                                </a>
                            </th>
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'call_number', 'direction' => request('sort') == 'call_number' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Call Number
                                    @include('partials.sort-icon', ['field' => 'call_number'])
                                </a>
                            </th>
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'location', 'direction' => request('sort') == 'location' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Location
                                    @include('partials.sort-icon', ['field' => 'location'])
                                </a>
                            </th>

                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'shelf_number', 'direction' => request('sort') == 'shelf_number' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Shelf
                                    @include('partials.sort-icon', ['field' => 'shelf_number'])
                                </a>
                            </th>
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'campus', 'direction' => request('sort') == 'campus' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Campus
                                    @include('partials.sort-icon', ['field' => 'campus'])
                                </a>
                            </th>
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => request('sort') == 'status' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Status
                                    @include('partials.sort-icon', ['field' => 'status'])
                                </a>
                            </th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                        <!-- Filter Row -->
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <td class="py-2 px-6">
                                <input type="text" name="accession_no" value="{{ request('accession_no') }}" placeholder="Acc No" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                            <td class="py-2 px-6">
                                <input type="text" name="barcode" value="{{ request('barcode') }}" placeholder="Barcode" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                            <td class="py-2 px-6">
                                <input type="text" name="title" value="{{ request('title') }}" placeholder="Title" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                            <td class="py-2 px-6">
                                <input type="text" name="author" value="{{ request('author') }}" placeholder="Author" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                            <td class="py-2 px-6">
                                <input type="text" name="call_number" value="{{ request('call_number') }}" placeholder="Call No" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                            <td class="py-2 px-6">
                                <input type="text" name="location" value="{{ request('location') }}" placeholder="Location" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                            <td class="py-2 px-6">
                                <input type="text" name="shelf_number" value="{{ request('shelf_number') }}" placeholder="Shelf No" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                            <td class="py-2 px-6">
                                <input type="text" name="campus" value="{{ request('campus') }}" placeholder="Campus" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                            </td>
                            <td class="py-2 px-6">
                                <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
                                    <option value="">All</option>
                                    <option value="available" {{ strtolower(request('status')) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="borrowed" {{ strtolower(request('status')) == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                                </select>
                            </td>
                            <td class="py-2 px-6 text-center whitespace-nowrap">
                                <button type="submit" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition">Filter</button>
                                <a href="{{ route('admin.library.research.index') }}" class="text-xs bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600 transition ml-1 inline-block">Clear</button>
                            </td>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse ($research as $item)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition"
                                id="research-row-{{ $item->accession_no }}">
                                <td class="py-4 px-6 font-medium text-gray-800">{{ $item->accession_no }}</td>
                                <td class="py-4 px-6 text-gray-600 italic">{{ $item->barcode ?? 'N/A' }}</td>
                                <td class="py-4 px-6">{{ $item->title }}</td>
                                <td class="py-4 px-6">{{ $item->author }}</td>
                                <td class="py-4 px-6">{{ $item->call_number }}</td>
                                <td class="py-4 px-6">{{ $item->location ?? 'N/A' }}</td>
                                <td class="py-4 px-6">{{ $item->shelf_number ?? 'N/A' }}</td>
                                <td class="py-4 px-6">{{ $item->campus ?? 'N/A' }}</td>
                                <td class="py-4 px-6">
                                    @if (strtolower($item->status) === 'available')
                                        <span
                                            class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs">{{ $item->status }}</span>
                                    @else
                                        <span
                                            class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex item-center justify-center gap-3">
                                        <button type="button" onclick="showEditModal({{ json_encode($item) }})"
                                            class="text-orange-600 hover:text-orange-800 transition" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button" onclick="deleteResearch('{{ $item->accession_no }}')"
                                            class="text-red-600 hover:text-red-800 transition" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-4 px-6 text-center text-gray-400 italic">No research in the database.
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
                Showing <span class="font-semibold text-gray-700">{{ $research->firstItem() ?? 0 }}</span>
                to <span class="font-semibold text-gray-700">{{ $research->lastItem() ?? 0 }}</span>
                of <span class="font-semibold text-gray-700">{{ $research->total() }}</span> research
            </div>
            <div class="pagination-container">
                {{ $research->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addResearchModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAddModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="addResearchForm" onsubmit="submitAddForm(event)">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Research</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Accession No</label>
                                <input type="text" name="accession_no" id="add_accession_no" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Barcode</label>
                                <input type="text" name="barcode" id="add_barcode"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2"
                                    placeholder="Optional barcode...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" name="title" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Author</label>
                                <input type="text" name="author" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Call Number</label>
                                <input type="text" name="call_number" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Location</label>
                                <select name="location" id="add_location"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="">-- Select Location --</option>
                                    <option value="Circulation">Circulation</option>
                                    <option value="Filipiniana">Filipiniana</option>
                                    <option value="Reserve">Reserve</option>
                                    <option value="Periodicals">Periodicals</option>
                                    <option value="Fiction">Fiction</option>
                                    <option value="Children's">Children's</option>
                                    <option value="Reference">Reference</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Shelf</label>
                                <select name="shelf_number" id="add_shelf_number"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="">-- No Specific Shelf --</option>
                                    @foreach ($shelves as $shelf)
                                        <option value="{{ $shelf->shelf_number }}">{{ $shelf->shelf_number }} {{ $shelf->description ? '(' . $shelf->description . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Campus</label>
                                <select name="campus" id="add_campus"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="">-- Select Campus --</option>
                                    <option value="DCC TED">DCC TED</option>
                                    <option value="DCC BED Highschool">DCC BED Highschool</option>
                                    <option value="DCC BED SeniorHighSchool">DCC BED SeniorHighSchool</option>
                                    <option value="DCC BED Elementary">DCC BED Elementary</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="add_status" name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="Available">Available</option>
                                    <option value="Borrowed">Borrowed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-emerald-700 text-white hover:bg-emerald-800 sm:ml-3 sm:w-auto sm:text-sm">Save
                            Research</button>
                        <button type="button" onclick="closeAddModal()"
                            class="mt-3 w-inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editResearchModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editResearchForm" onsubmit="submitEditForm(event)">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Research</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Accession No</label>
                                <input type="text" id="edit_accession_no_display" name="accession_no"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                <input type="hidden" id="edit_accession_no_original">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Barcode</label>
                                <input type="text" id="edit_barcode" name="barcode"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" id="edit_title" name="title" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Author</label>
                                <input type="text" id="edit_author" name="author" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Call Number</label>
                                <input type="text" id="edit_call_number" name="call_number" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Location</label>
                                <select name="location" id="edit_location"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="">-- Select Location --</option>
                                    <option value="Circulation">Circulation</option>
                                    <option value="Filipiniana">Filipiniana</option>
                                    <option value="Reserve">Reserve</option>
                                    <option value="Periodicals">Periodicals</option>
                                    <option value="Fiction">Fiction</option>
                                    <option value="Children's">Children's</option>
                                    <option value="Reference">Reference</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Shelf</label>
                                <select name="shelf_number" id="edit_shelf_number"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="">-- No Specific Shelf --</option>
                                    @foreach ($shelves as $shelf)
                                        <option value="{{ $shelf->shelf_number }}">{{ $shelf->shelf_number }} {{ $shelf->description ? '(' . $shelf->description . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Campus</label>
                                <select name="campus" id="edit_campus"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="">-- Select Campus --</option>
                                    <option value="DCC TED">DCC TED</option>
                                    <option value="DCC BED Highschool">DCC BED Highschool</option>
                                    <option value="DCC BED SeniorHighSchool">DCC BED SeniorHighSchool</option>
                                    <option value="DCC BED Elementary">DCC BED Elementary</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="edit_status" name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="Available">Available</option>
                                    <option value="Borrowed">Borrowed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-emerald-700 text-white hover:bg-emerald-800 sm:ml-3 sm:w-auto sm:text-sm">Update
                            Research</button>
                        <button type="button" onclick="closeEditModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showAlert(message, type = 'success') {
            const bg = type === 'success' ? 'bg-green-100 border-green-500 text-green-700' :
                'bg-red-100 border-red-500 text-red-700';
            document.getElementById('alert-container').innerHTML = `
            <div class="mb-4 p-4 ${bg} border-l-4 flex justify-between items-center transition-all duration-500 rounded">
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="font-bold">&times;</button>
            </div>
        `;
        }

        function showAddModal() {
            document.getElementById('addResearchModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('add_accession_no').focus(), 100);
        }

        function closeAddModal() {
            document.getElementById('addResearchModal').classList.add('hidden');
            document.getElementById('addResearchForm').reset();
        }

        function showEditModal(research) {
            console.log('Editing research:', research);
            const accNo = research.accession_no || research.id;

            // This is for the URL (the original/current primary key)
            document.getElementById('edit_accession_no_original').value = accNo;

            // This is for editing (the potentially new primary key)
            document.getElementById('edit_accession_no_display').value = accNo;

            document.getElementById('add_barcode').value = research.barcode || '';
            document.getElementById('edit_title').value = research.title;
            document.getElementById('edit_author').value = research.author;
            document.getElementById('edit_call_number').value = research.call_number || research.callnumber || '';
            // Normalize location (handle database discrepancies like 'Periodical', 'Reserved', etc.)
            let loc = research.location || '';
            if (loc === 'Periodical') loc = 'Periodicals';
            if (loc === 'Reserved') loc = 'Reserve';
            if (loc === 'Filipiana' || loc === 'Filipinana') loc = 'Filipiniana';
            document.getElementById('edit_location').value = loc;

            document.getElementById('edit_shelf_number').value = research.shelf_number || '';

            document.getElementById('edit_campus').value = research.campus || '';

            // Normalize status to match option values 'Available' or 'Borrowed'
            let status = research.status || 'Available';
            if (status.toLowerCase() === 'available') status = 'Available';
            if (status.toLowerCase() === 'borrowed') status = 'Borrowed';
            document.getElementById('edit_status').value = status;
            document.getElementById('editResearchModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editResearchModal').classList.add('hidden');
        }

        async function submitAddForm(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data._token = '{{ csrf_token() }}';

            try {
                const res = await fetch('{{ route('admin.library.research.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                if (result.success) {
                    showAlert(result.message);
                    closeAddModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert(result.message || 'Error adding research', 'error');
                }
            } catch (err) {
                showAlert('Operation failed.', 'error');
            }
        }

        async function submitEditForm(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            // Use the original accession number for the route
            const originalAccNo = document.getElementById('edit_accession_no_original').value;
            if (!originalAccNo) {
                showAlert('Error: Original Accession Number is missing.', 'error');
                return;
            }

            const url = '{{ route('admin.library.research.update', ':id') }}'.replace(':id', originalAccNo);

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ...data,
                        _method: 'PUT'
                    })
                });
                const result = await res.json();
                if (result.success) {
                    showAlert(result.message);
                    closeEditModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert(result.message || 'Error updating research', 'error');
                }
            } catch (err) {
                showAlert('Operation failed.', 'error');
            }
        }

        async function deleteResearch(accession_no) {
            if (!confirm('Are you sure you want to delete this research?')) return;
            const url = '{{ route('admin.library.research.destroy', ':id') }}'.replace(':id', accession_no);
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                });
                const result = await res.json();
                if (result.success) {
                    showAlert(result.message);
                    const row = document.getElementById('research-row-' + accession_no);
                    if (row) row.remove();
                } else {
                    showAlert(result.message || 'Error deleting research', 'error');
                }
            } catch (err) {
                showAlert('Operation failed.', 'error');
            }
        }
    </script>
@endsection