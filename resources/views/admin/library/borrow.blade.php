@extends('layouts.app')

@section('title', 'Borrow Book')
@section('header', 'Library - Borrow Book')

@section('content')
    <div class="flex gap-6 items-start">

        {{-- ─── LEFT: Borrow Form ─── --}}
        <div class="flex-1 min-w-0">
            <div id="alert-container"></div>

            <div class="card bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Borrow Book Scanner</h2>
                    <p class="text-gray-500 mt-2">Please fill in the details, then scan the Book Barcode.</p>
                </div>

                <form id="borrowForm" onsubmit="submitBorrowForm(event)">
                    <div class="space-y-6">

                        <!-- Borrower ID -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Borrower ID (Student SID / Employee ID / RFID)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <input type="text" id="borrower_id" name="borrower_id" required autofocus
                                    autocomplete="off"
                                    class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-lg py-3 border bg-gray-50 focus:bg-white transition"
                                    placeholder="Scan or type ID...">
                            </div>
                        </div>

                        <!-- Borrower Type -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="borrow_type">Borrower
                                Type</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <select id="borrow_type" name="borrow_type" required autocomplete="off"
                                    class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-lg py-3 border bg-gray-50 focus:bg-white transition">
                                    <option value="">Select Borrower Type</option>
                                    <option value="Student">Student</option>
                                    <option value="Faculty">Faculty</option>
                                    <option value="Staff">Staff</option>
                                </select>
                            </div>
                        </div>

                        <!-- Book Section -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="book_section">Book
                                Section</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <select id="book_section" name="book_section" required autocomplete="off"
                                    class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-lg py-3 border bg-gray-50 focus:bg-white transition">
                                    <option value="">Select Book Section</option>
                                    <option value="Reserved">Reserved</option>
                                    <option value="Filipiniana">Filipiniana</option>
                                    <option value="Circulation">Circulation</option>
                                    <option value="Fiction">Fiction</option>
                                    <option value="Thesis & Dissertation">Thesis & Dissertation</option>
                                </select>
                            </div>
                        </div>

                        <!-- Borrow Period -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="borrow_period">Borrow
                                Period</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <select id="borrow_period" name="borrow_period" required autocomplete="off"
                                    class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-lg py-3 border bg-gray-50 focus:bg-white transition">
                                    <option value="">Select Borrow Period</option>
                                    <option value="30 minutes">30 minutes</option>
                                    <option value="1 day">1 day</option>
                                    <option value="3 days">3 days</option>
                                    <option value="5 days">5 days</option>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                    <option value="Inside Reading">Inside Reading</option>
                                    <option value="Summer Class">Summer Class</option>
                                </select>
                            </div>
                        </div>

                        <!-- Book Barcode -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Book Barcode / Accession
                                No</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                                <input type="text" id="accession_no" name="accession_no" required autocomplete="off"
                                    class="pl-10 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-lg py-3 border bg-gray-50 focus:bg-white transition"
                                    placeholder="Scan book barcode...">
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" id="submitBtn"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition">
                                Process Borrowing
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ─── RIGHT: Receipt Preview ─── --}}
        <div class="w-80 flex-shrink-0">
            <div id="receiptPanel" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

                {{-- Header --}}
                <div class="bg-emerald-600 px-5 py-4 text-white">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="h-5 w-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="font-bold text-lg tracking-wide">Borrow Receipt</span>
                    </div>
                    <p class="text-emerald-100 text-xs">DCC Library System</p>
                </div>

                {{-- Empty state --}}
                <div id="receiptEmpty" class="flex flex-col items-center justify-center py-14 px-6 text-center">
                    <svg class="h-14 w-14 text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-400 text-sm">Receipt will appear here after a successful transaction.</p>
                </div>

                {{-- Filled receipt --}}
                <div id="receiptContent" class="hidden px-5 py-5 space-y-4 text-sm">

                    {{-- Date --}}
                    <div class="flex justify-between text-xs text-gray-400 border-b border-dashed border-gray-200 pb-3">
                        <span id="r-date"></span>
                    </div>

                    {{-- Borrower --}}
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Borrower</p>
                        <p id="r-name" class="font-bold text-gray-800 text-base"></p>
                        <div class="flex gap-2 mt-1 flex-wrap">
                            <span id="r-borrow-type"
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"></span>
                            <span id="r-borrower-id" class="text-gray-400 text-xs self-center font-mono"></span>
                        </div>
                        <div id="r-course-year-wrap" class="hidden mt-1 text-xs text-gray-500">
                            <span id="r-course"></span> &bull; <span id="r-year"></span> Year
                        </div>
                    </div>

                    {{-- Book --}}
                    <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">Book Details</p>
                        <p id="r-title" class="font-semibold text-gray-800"></p>
                        <p class="text-gray-500"><span class="font-medium">Author:</span> <span id="r-author"></span>
                        </p>
                        <p class="text-gray-500"><span class="font-medium">Call No:</span> <span id="r-call-no"></span>
                        </p>
                        <p class="text-gray-500"><span class="font-medium">Accession:</span> <span
                                id="r-accession"></span></p>
                        <p class="text-gray-500"><span class="font-medium">Section:</span> <span id="r-section"></span>
                        </p>
                    </div>

                    {{-- Dates --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Borrowed</p>
                            <p id="r-borrowed" class="font-semibold text-gray-700 text-xs"></p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-amber-500 uppercase tracking-widest mb-1">Due Date</p>
                            <p id="r-due" class="font-bold text-amber-700 text-xs"></p>
                        </div>
                    </div>

                    {{-- Period badge --}}
                    <div class="text-center">
                        <span id="r-period"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"></span>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-dashed border-gray-200"></div>

                    {{-- Print button --}}
                    <button onclick="printReceipt()"
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden printable area --}}
    <div id="printArea" class="hidden">
        <style>
            @media print {
                @page {
                    size: 58mm auto;
                    margin: 0;
                }

                body * {
                    visibility: hidden;
                }

                #printArea,
                #printArea * {
                    visibility: visible;
                }

                #printArea {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 58mm;
                    font-family: 'Consolas', 'Courier New', Courier, monospace;
                    background: #fff;
                    color: #000;
                }

                /* Force all text to black and bold for high thermal print contrast */
                #printArea * {
                    color: #000 !important;
                    font-weight: bold !important;
                }
            }
        </style>

        {{-- Thermal Receipt Content --}}
        <div style="width: 100%; box-sizing: border-box; font-family: 'Consolas', 'Courier New', Courier, monospace; color: #000; padding: 3mm 2mm; border: 2px solid #000;">

            {{-- Header --}}
            <div style="text-align: center; padding-bottom: 6px; border-bottom: 2px solid #000; margin-bottom: 8px;">
                <div style="font-size: 20px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.2;">DCC LIBRARY SYSTEM</div>
                <div style="font-size: 15px; font-weight: bold; margin-top: 3px;">Borrow Receipt</div>
                <div id="p-date" style="font-size: 14px; margin-top: 2px; font-weight: bold;"></div>
            </div>

            {{-- Borrower Section --}}
            <div style="margin-bottom: 8px;">
                <div style="font-size: 14px; font-weight: bold; letter-spacing: 1px; border-bottom: 1.5px solid #000; padding-bottom: 2px; margin-bottom: 5px;">BORROWER</div>
                <div id="p-name" style="font-size: 18px; font-weight: bold; line-height: 1.2; margin-bottom: 4px;"></div>
                <div style="font-size: 15px; line-height: 1.5;">Type: <span id="p-borrow-type"></span></div>
                <div style="font-size: 15px; line-height: 1.5;">ID: <span id="p-borrower-id"></span></div>
                <div id="p-course-year-wrap" style="font-size: 15px; line-height: 1.5;"><span id="p-course"></span> &bull; <span id="p-year"></span> Year</div>
            </div>

            {{-- Book Section --}}
            <div style="margin-bottom: 8px;">
                <div style="font-size: 14px; font-weight: bold; letter-spacing: 1px; border-bottom: 1.5px solid #000; padding-bottom: 2px; margin-bottom: 5px;">BOOK</div>
                <div id="p-title" style="font-size: 18px; font-weight: bold; line-height: 1.2; margin-bottom: 4px;"></div>
                <div style="font-size: 15px; line-height: 1.5;">Author: <span id="p-author"></span></div>
                <div style="font-size: 15px; line-height: 1.5;">Call No: <span id="p-call-no"></span></div>
                <div style="font-size: 15px; line-height: 1.5;">Accession: <span id="p-accession"></span></div>
                <div style="font-size: 15px; line-height: 1.5;">Section: <span id="p-section"></span></div>
            </div>

            {{-- Dates Section --}}
            <div style="margin-bottom: 8px;">
                <div style="font-size: 14px; font-weight: bold; letter-spacing: 1px; border-bottom: 1.5px solid #000; padding-bottom: 2px; margin-bottom: 5px;">BORROW INFO</div>
                <div style="font-size: 15px; line-height: 1.5;">Date Borrowed: <span id="p-borrowed"></span></div>
                <div style="font-size: 15px; line-height: 1.5;">Due Date: <span id="p-due"></span></div>
                <div style="font-size: 15px; line-height: 1.5;">Borrow Period: <span id="p-period"></span></div>
            </div>

            {{-- Footer --}}
            <div style="border-top: 2px solid #000; padding-top: 6px; text-align: center;">
                <div style="font-size: 13px; font-weight: bold; line-height: 1.4; margin-bottom: 2px;">Please return the book on or before the due date.</div>
                <div style="font-size: 13px; font-weight: bold; line-height: 1.4;">Fine: Php 5.00 per day overdue.</div>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('borrower_id').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = this.value.trim();
                if (val !== '') document.getElementById('accession_no').focus();
            }
        });

        function showAlert(message, type = 'success') {
            const bg = type === 'success' ?
                'bg-green-100 border-green-500 text-green-700' :
                'bg-red-100 border-red-500 text-red-700';
            document.getElementById('alert-container').innerHTML = `
            <div class="mb-4 p-4 ${bg} border-l-4 rounded shadow-sm text-base flex justify-between items-center">
                <span><strong>${type === 'success' ? 'Success!' : 'Error:'}</strong> ${message}</span>
                <button onclick="this.parentElement.remove()" class="font-bold ml-4">&times;</button>
            </div>`;
            if (type === 'success') {
                setTimeout(() => {
                    document.getElementById('alert-container').innerHTML = '';
                }, 4000);
            }
        }

        function fmt(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function fmtFull(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            return d.toLocaleString('en-PH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function fillReceipt(result) {
            const t = result.transaction;
            const b = result.book;
            const now = new Date();

            // Preview panel
            document.getElementById('r-date').textContent = now.toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            // transaction ID removed from receipt display
            document.getElementById('r-name').textContent = result.borrower_name;
            document.getElementById('r-borrow-type').textContent = t.borrow_type;
            document.getElementById('r-borrower-id').textContent = t.borrower_id;

            // Year & course (students only)
            const hasCourseYear = result.borrower_year || result.borrower_course;
            const cyWrap = document.getElementById('r-course-year-wrap');
            if (hasCourseYear) {
                document.getElementById('r-course').textContent = result.borrower_course || '';
                document.getElementById('r-year').textContent = result.borrower_year || '';
                cyWrap.classList.remove('hidden');
            } else {
                cyWrap.classList.add('hidden');
            }

            document.getElementById('r-title').textContent = b.title;
            document.getElementById('r-author').textContent = b.author;
            document.getElementById('r-call-no').textContent = b.call_number;
            document.getElementById('r-accession').textContent = b.accession_no;
            document.getElementById('r-section').textContent = t.book_section;
            document.getElementById('r-borrowed').textContent = fmt(t.date_borrowed);
            document.getElementById('r-due').textContent = fmtFull(t.due_date);
            document.getElementById('r-period').textContent = '⏱ ' + document.getElementById('borrow_period').value;

            // Print area
            document.getElementById('p-date').textContent = now.toLocaleString('en-PH');
            document.getElementById('p-name').textContent = result.borrower_name;
            document.getElementById('p-borrow-type').textContent = t.borrow_type;
            document.getElementById('p-borrower-id').textContent = t.borrower_id;
            const pCyWrap = document.getElementById('p-course-year-wrap');
            if (result.borrower_year || result.borrower_course) {
                document.getElementById('p-course').textContent = result.borrower_course || '';
                document.getElementById('p-year').textContent = result.borrower_year || '';
                pCyWrap.style.display = '';
            } else {
                pCyWrap.style.display = 'none';
            }
            document.getElementById('p-title').textContent = b.title;
            document.getElementById('p-author').textContent = b.author;
            document.getElementById('p-call-no').textContent = b.call_number;
            document.getElementById('p-accession').textContent = b.accession_no;
            document.getElementById('p-section').textContent = t.book_section;
            document.getElementById('p-borrowed').textContent = fmt(t.date_borrowed);
            document.getElementById('p-due').textContent = fmtFull(t.due_date);
            document.getElementById('p-period').textContent = document.getElementById('borrow_period').value;

            document.getElementById('receiptEmpty').classList.add('hidden');
            document.getElementById('receiptContent').classList.remove('hidden');
        }

        function printReceipt() {
            document.getElementById('printArea').classList.remove('hidden');
            window.print();
            document.getElementById('printArea').classList.add('hidden');
        }

        async function submitBorrowForm(e) {
            e.preventDefault();

            const borrower_id = document.getElementById('borrower_id').value.trim();
            const accession_no = document.getElementById('accession_no').value.trim();
            const borrow_type = document.getElementById('borrow_type').value;
            const book_section = document.getElementById('book_section').value;
            const borrow_period = document.getElementById('borrow_period').value;

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Processing...';

            try {
                const res = await fetch('{{ route('admin.library.borrow.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        borrower_id,
                        accession_no,
                        borrow_type,
                        book_section,
                        borrow_period
                    })
                });
                const result = await res.json();

                if (result.success) {
                    fillReceipt(result);
                    showAlert(
                        `Book borrowed successfully. Due: ${new Date(result.transaction.due_date).toLocaleString('en-PH')}`
                        );
                    document.getElementById('borrowForm').reset();
                    document.getElementById('borrower_id').focus();
                } else {
                    showAlert(result.message || 'Operation failed', 'error');
                    document.getElementById('accession_no').select();
                }
            } catch (err) {
                showAlert('A network error occurred.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Process Borrowing';
            }
        }
    </script>
@endsection
