@extends('layouts.app')

@section('title', 'Book Database - Elementary')
@section('header', 'Library - Book Database (Elementary)')

@section('content')
    <!-- Alerts -->
    <div id="alert-container"></div>

    <div class="card bg-white p-6 rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-gray-700 font-bold text-lg">Master List of Books &mdash; Elementary</h3>

            <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                <div class="relative w-full md:w-80">
                    <form method="GET" action="{{ route('admin.library.books.index') }}">
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
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="w-full py-2 pl-10 {{ request('search') ? 'pr-10' : 'pr-4' }} text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600 focus:ring-1 focus:ring-green-600 transition"
                            placeholder="Search books...">
                        @if(request('search'))
                            <a href="{{ route('admin.library.books.index', request()->except('search', 'page')) }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    Add Collections
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <form id="filterForm" method="GET" action="{{ route('admin.library.books.index') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="direction" value="{{ request('direction') }}">

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'accession_number', 'direction' => request('sort') == 'accession_number' && request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 group">
                                    Accession No
                                    @include('partials.sort-icon', ['field' => 'accession_number'])
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
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                        <!-- Filter Row -->
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <td class="py-2 px-6">
                                <input type="text" name="accession_no" value="{{ request('accession_no') }}" placeholder="Acc No" class="text-xs border border-gray-300 rounded p-1 w-full focus:ring-1 focus:ring-green-500 outline-none">
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
                            <td class="py-2 px-6 text-center whitespace-nowrap">
                                <button type="submit" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition">Filter</button>
                                <a href="{{ route('admin.library.books.index') }}" class="text-xs bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600 transition ml-1 inline-block">Clear</a>
                            </td>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse ($elemBooks as $book)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition"
                                id="book-row-{{ $book->accession_number }}">
                                <td class="py-4 px-6 font-medium text-gray-800">{{ $book->accession_number ?? 'N/A' }}</td>
                                <td class="py-4 px-6">{{ $book->title }}</td>
                                <td class="py-4 px-6">{{ $book->author }}</td>
                                <td class="py-4 px-6">{{ $book->call_number ?? 'N/A' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex item-center justify-center gap-3">
                                        <button type="button" onclick="showEditModal({{ json_encode($book) }})"
                                            class="text-orange-600 hover:text-orange-800 transition" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button" onclick="deleteBook('{{ $book->accession_number }}')"
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
                                <td colspan="5" class="py-4 px-6 text-center text-gray-400 italic">No books in the database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-gray-100 pt-6">
            <div class="text-sm text-gray-500">
                Showing <span class="font-semibold text-gray-700">{{ $elemBooks->firstItem() ?? 0 }}</span>
                to <span class="font-semibold text-gray-700">{{ $elemBooks->lastItem() ?? 0 }}</span>
                of <span class="font-semibold text-gray-700">{{ $elemBooks->total() }}</span> books
            </div>
            <div class="pagination-container">
                {{ $elemBooks->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addBookModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAddModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="addBookForm" onsubmit="submitAddForm(event)">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Book</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Accession No</label>
                                <input type="text" name="accession_no" id="add_accession_no" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
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
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-emerald-700 text-white hover:bg-emerald-800 sm:ml-3 sm:w-auto sm:text-sm">Save Book</button>
                        <button type="button" onclick="closeAddModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editBookModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editBookForm" onsubmit="submitEditForm(event)">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Book</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Accession No</label>
                                <input type="text" id="edit_accession_no_display" name="accession_no"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                <input type="hidden" id="edit_accession_no_original">
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
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-emerald-700 text-white hover:bg-emerald-800 sm:ml-3 sm:w-auto sm:text-sm">Update Book</button>
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
            document.getElementById('addBookModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('add_accession_no').focus(), 100);
        }

        function closeAddModal() {
            document.getElementById('addBookModal').classList.add('hidden');
            document.getElementById('addBookForm').reset();
        }

        function showEditModal(book) {
            const accNo = book.accession_number || book.id;
            document.getElementById('edit_accession_no_original').value = accNo;
            document.getElementById('edit_accession_no_display').value = accNo;
            document.getElementById('edit_title').value = book.title || '';
            document.getElementById('edit_author').value = book.author || '';
            document.getElementById('edit_call_number').value = book.call_number || '';
            document.getElementById('editBookModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editBookModal').classList.add('hidden');
        }

        async function submitAddForm(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data._token = '{{ csrf_token() }}';

            try {
                const res = await fetch('{{ route('admin.library.books.store') }}', {
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
                    showAlert(result.message || 'Error adding book', 'error');
                }
            } catch (err) {
                showAlert('Operation failed.', 'error');
            }
        }

        async function submitEditForm(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const originalAccNo = document.getElementById('edit_accession_no_original').value;
            if (!originalAccNo) {
                showAlert('Error: Original Accession Number is missing.', 'error');
                return;
            }

            const url = '{{ route('admin.library.books.update', ':id') }}'.replace(':id', originalAccNo);

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
                    showAlert(result.message || 'Error updating book', 'error');
                }
            } catch (err) {
                showAlert('Operation failed.', 'error');
            }
        }

        async function deleteBook(accession_number) {
            if (!confirm('Are you sure you want to delete this book?')) return;
            const url = '{{ route('admin.library.books.destroy', ':id') }}'.replace(':id', accession_number);
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });
                const result = await res.json();
                if (result.success) {
                    showAlert(result.message);
                    const row = document.getElementById('book-row-' + accession_number);
                    if (row) row.remove();
                } else {
                    showAlert(result.message || 'Error deleting book', 'error');
                }
            } catch (err) {
                showAlert('Operation failed.', 'error');
            }
        }
    </script>
@endsection
