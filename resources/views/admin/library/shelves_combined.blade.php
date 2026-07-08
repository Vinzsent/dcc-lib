@extends('layouts.app')

@section('title', 'All Shelves')
@section('header', 'Library - All Shelves (TED + BED)')

@php
    // Color-coded campus labels for the combined Master view
    $campusStyles = [
        'DCC TED'                 => ['label' => 'TED — DCC TED',           'cls' => 'bg-emerald-100 text-emerald-800'],
        'DCC BED Highschool'      => ['label' => 'BED — High School',       'cls' => 'bg-amber-100 text-amber-800'],
        'DCC BED SeniorHighSchool'=> ['label' => 'BED — Senior High School','cls' => 'bg-orange-100 text-orange-800'],
        'DCC BED Elementary'      => ['label' => 'BED — Elementary',        'cls' => 'bg-yellow-100 text-yellow-800'],
    ];

    $allCampuses = [
        'DCC TED'                  => 'TED — DCC TED',
        'DCC BED Highschool'       => 'BED — High School',
        'DCC BED SeniorHighSchool' => 'BED — Senior High School',
        'DCC BED Elementary'       => 'BED — Elementary',
    ];

    $total   = $shelves->count();
    $tedCount = $shelves->where('campus', 'DCC TED')->count();
    $bedCount = $total - $tedCount;
@endphp

@section('content')
    <!-- Alerts -->
    <div id="alert-container"></div>

    <div class="card bg-white p-6 rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h3 class="text-gray-700 font-bold text-lg">All Shelves</h3>
                <p class="text-xs text-gray-400 mt-1">TED (Tertiary Education) &amp; BED (Basic Education)</p>
            </div>

            <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                <!-- Campus filter -->
                <select id="campusFilter"
                    class="border-gray-300 rounded-lg border p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="">All Campuses</option>
                    @foreach ($allCampuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <button onclick="showAddModal()"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Add Shelf
                </button>
            </div>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-gray-50 rounded-lg p-3 text-center border border-gray-100">
                <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">Total</div>
            </div>
            <div class="bg-emerald-50 rounded-lg p-3 text-center border border-emerald-100">
                <div class="text-2xl font-bold text-emerald-700">{{ $tedCount }}</div>
                <div class="text-xs text-emerald-600 uppercase tracking-wide">TED</div>
            </div>
            <div class="bg-amber-50 rounded-lg p-3 text-center border border-amber-100">
                <div class="text-2xl font-bold text-amber-700">{{ $bedCount }}</div>
                <div class="text-xs text-amber-600 uppercase tracking-wide">BED</div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                        <th class="py-3 px-6">Shelf Number</th>
                        <th class="py-3 px-6">Description</th>
                        <th class="py-3 px-6">Campus</th>
                        <th class="py-3 px-6 text-center">Books Count</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($shelves as $shelf)
                        @php $style = $campusStyles[$shelf->campus] ?? null; @endphp
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition shelf-row" data-campus="{{ $shelf->campus ?? '' }}" id="shelf-row-{{ $shelf->id }}">
                            <td class="py-4 px-6 font-medium text-gray-800">{{ $shelf->shelf_number }}</td>
                            <td class="py-4 px-6">{{ $shelf->description ?? 'N/A' }}</td>
                            <td class="py-4 px-6">
                                <span class="font-semibold py-1 px-3 rounded-full text-xs {{ $style['cls'] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $style['label'] ?? ($shelf->campus ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="bg-blue-100 text-blue-800 font-semibold py-1 px-3 rounded-full text-xs">
                                    {{ $shelf->books()->count() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex item-center justify-center gap-3">
                                    <button type="button" onclick="showEditModal({{ json_encode($shelf) }})"
                                        class="text-orange-600 hover:text-orange-800 transition" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" onclick="deleteShelf({{ $shelf->id }})"
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
                            <td colspan="5" class="py-4 px-6 text-center text-gray-400 italic">No shelves found in the database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addShelfModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAddModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="addShelfForm" onsubmit="submitAddForm(event)">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Shelf</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Shelf Number <span class="text-red-500">*</span></label>
                                <input type="text" name="shelf_number" id="add_shelf_number" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2"
                                    placeholder="e.g. Shelf A-1">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" name="description"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2"
                                    placeholder="Optional details...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Campus <span class="text-red-500">*</span></label>
                                <select name="campus" id="add_campus"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="">-- Select Campus --</option>
                                    @foreach ($allCampuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-emerald-700 text-white hover:bg-emerald-800 sm:ml-3 sm:w-auto sm:text-sm">Save Shelf</button>
                        <button type="button" onclick="closeAddModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editShelfModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editShelfForm" onsubmit="submitEditForm(event)">
                    <input type="hidden" id="edit_shelf_id">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Shelf</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Shelf Number <span class="text-red-500">*</span></label>
                                <input type="text" id="edit_shelf_number" name="shelf_number" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" id="edit_description" name="description"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Campus <span class="text-red-500">*</span></label>
                                <select name="campus" id="edit_campus"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                    <option value="">-- Select Campus --</option>
                                    @foreach ($allCampuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md px-4 py-2 bg-emerald-700 text-white hover:bg-emerald-800 sm:ml-3 sm:w-auto sm:text-sm">Update Shelf</button>
                        <button type="button" onclick="closeEditModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showAlert(message, type = 'success') {
            const bg = type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';
            document.getElementById('alert-container').innerHTML = `
                <div class="mb-4 p-4 ${bg} border-l-4 flex justify-between items-center transition-all duration-500 rounded">
                    <span>${message}</span>
                    <button onclick="this.parentElement.remove()" class="font-bold">&times;</button>
                </div>
            `;
        }

        function showAddModal() {
            document.getElementById('addShelfModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('add_shelf_number').focus(), 100);
        }

        function closeAddModal() {
            document.getElementById('addShelfModal').classList.add('hidden');
            document.getElementById('addShelfForm').reset();
        }

        function showEditModal(shelf) {
            document.getElementById('edit_shelf_id').value = shelf.id;
            document.getElementById('edit_shelf_number').value = shelf.shelf_number;
            document.getElementById('edit_description').value = shelf.description || '';
            if (document.getElementById('edit_campus')) {
                document.getElementById('edit_campus').value = shelf.campus || '';
            }
            document.getElementById('editShelfModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editShelfModal').classList.add('hidden');
        }

        // Campus filter dropdown (client-side)
        document.getElementById('campusFilter').addEventListener('change', function () {
            const value = this.value;
            document.querySelectorAll('.shelf-row').forEach(row => {
                row.style.display = (!value || row.dataset.campus === value) ? '' : 'none';
            });
        });

        async function submitAddForm(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data._token = '{{ csrf_token() }}';

            try {
                const res = await fetch('{{ route('admin.library.shelves.store') }}', {
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
                    showAlert(result.message || 'Error adding shelf', 'error');
                }
            } catch (err) {
                showAlert('Operation failed.', 'error');
            }
        }

        async function submitEditForm(e) {
            e.preventDefault();
            const id = document.getElementById('edit_shelf_id').value;
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const url = '{{ route('admin.library.shelves.update', ':id') }}'.replace(':id', id);

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
                    showAlert(result.message || 'Error updating shelf', 'error');
                }
            } catch (err) {
                showAlert('Operation failed.', 'error');
            }
        }

        async function deleteShelf(id) {
            if (!confirm('Are you sure you want to delete this shelf?')) return;
            const url = '{{ route('admin.library.shelves.destroy', ':id') }}'.replace(':id', id);
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
                    const row = document.getElementById('shelf-row-' + id);
                    if (row) row.remove();
                } else {
                    showAlert(result.message || 'Error deleting shelf', 'error');
                }
            } catch (err) {
                showAlert('Operation failed.', 'error');
            }
        }
    </script>
@endsection
