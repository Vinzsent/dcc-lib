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

                        <!-- Add Book Section -->
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-150 space-y-4">
                            <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Scan & Add Book</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Book Section -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1" for="book_section">Book Section</label>
                                    <select id="book_section" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2 border focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                                        <option value="Reserved">Reserved</option>
                                        <option value="Filipiniana" selected>Filipiniana</option>
                                        <option value="Circulation">Circulation</option>
                                        <option value="Fiction">Fiction</option>
                                        <option value="Thesis & Dissertation">Thesis & Dissertation</option>
                                    </select>
                                </div>

                                <!-- Borrow Period -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1" for="borrow_period">Borrow Period</label>
                                    <select id="borrow_period" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2 border focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                                        <option value="30 minutes">30 minutes</option>
                                        <option value="1 day" selected>1 day</option>
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
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Book Barcode / Accession No</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="accession_no" autocomplete="off" class="pl-9 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm p-2.5 border" placeholder="Scan barcode to add to cart...">
                                    </div>
                                    <button type="button" onclick="scanAndAddBook()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg> Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Scanned Books Cart -->
                        <div id="cartSection" class="hidden border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                <span class="font-bold text-gray-700 text-sm flex items-center gap-2">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    Books in Cart (<span id="cartCount">0</span>)
                                </span>
                                <button type="button" onclick="clearCart()" class="text-xs text-red-600 hover:text-red-700 font-bold transition">Clear All</button>
                            </div>
                            <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto" id="cartItems">
                                <!-- Cart items will be generated here -->
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" id="submitBtn" disabled
                                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
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

                    {{-- Books List Container --}}
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">Books Borrowed</p>
                        <div id="r-books-list" class="space-y-3">
                            <!-- Dynamically generated book rows -->
                        </div>
                    </div>

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
        <div style="width: 100%; box-sizing: border-box; font-family: 'Consolas', 'Courier New', Courier, monospace; color: #000; padding: 2mm 1mm; border: 1px dashed #000;">

            {{-- Header --}}
            <div style="text-align: center; padding-bottom: 4px; border-bottom: 1.5px solid #000; margin-bottom: 6px;">
                <div style="font-size: 13px; font-weight: bold; letter-spacing: 0.5px; line-height: 1.2;">DCC LIBRARY SYSTEM</div>
                <div style="font-size: 11px; font-weight: bold; margin-top: 2px;">Borrow Receipt</div>
                <div id="p-date" style="font-size: 10px; margin-top: 1px; font-weight: bold;"></div>
            </div>

            {{-- Borrower Section --}}
            <div style="margin-bottom: 6px;">
                <div style="font-size: 10px; font-weight: bold; letter-spacing: 0.5px; border-bottom: 1px solid #000; padding-bottom: 1px; margin-bottom: 3px;">BORROWER</div>
                <div id="p-name" style="font-size: 11px; font-weight: bold; line-height: 1.2; margin-bottom: 2px;"></div>
                <div style="font-size: 10px; line-height: 1.3;">Type: <span id="p-borrow-type"></span></div>
                <div style="font-size: 10px; line-height: 1.3;">ID: <span id="p-borrower-id"></span></div>
                <div id="p-course-year-wrap" style="font-size: 10px; line-height: 1.3;"><span id="p-course"></span> &bull; <span id="p-year"></span> Year</div>
            </div>

            {{-- Books Section --}}
            <div style="margin-bottom: 6px;">
                <div style="font-size: 10px; font-weight: bold; letter-spacing: 0.5px; border-bottom: 1px solid #000; padding-bottom: 1px; margin-bottom: 3px;">BOOKS BORROWED</div>
                <div id="p-books-container" style="font-size: 10px; line-height: 1.3;">
                    <!-- Books listing will be generated here -->
                </div>
            </div>

            {{-- Borrow Info Section --}}
            <div style="margin-bottom: 6px; border-top: 1px solid #000; padding-top: 4px;">
                <div style="font-size: 10px; line-height: 1.3;">Date Borrowed: <span id="p-borrowed"></span></div>
                <div style="font-size: 9px; line-height: 1.3; color: #555;">Please see due date(s) per book above.</div>
            </div>

            {{-- Footer --}}
            <div style="border-top: 1.5px solid #000; padding-top: 4px; text-align: center;">
                <div style="font-size: 9px; font-weight: bold; line-height: 1.3; margin-bottom: 2px;">Please return the books on or before their respective due dates.</div>
                <div id="p-fine-rules-summary" style="font-size: 9px; font-weight: bold; line-height: 1.3;">Fine Rates: Php 5.00 per day overdue.</div>
            </div>

        </div>
    </div>

    <script>
        let bookCart = [];

        // Focus setup
        document.getElementById('borrower_id').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = this.value.trim();
                if (val !== '') document.getElementById('accession_no').focus();
            }
        });

        document.getElementById('accession_no').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                scanAndAddBook();
            }
        });

        async function scanAndAddBook() {
            const barcodeInput = document.getElementById('accession_no');
            const accession_no = barcodeInput.value.trim();
            const book_section = document.getElementById('book_section').value;
            const borrow_period = document.getElementById('borrow_period').value;

            if (!accession_no) {
                showAlert('Please enter or scan a book barcode.', 'error');
                return;
            }

            // Check if book already in cart
            if (bookCart.some(book => book.accession_no === accession_no || book.barcode === accession_no)) {
                showAlert('This book is already in the cart.', 'error');
                barcodeInput.value = '';
                barcodeInput.focus();
                return;
            }

            // Fetch details from server to validate
            try {
                const res = await fetch('{{ route('admin.library.borrow.check-book') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ accession_no })
                });

                const result = await res.json();
                if (result.success) {
                    const book = result.book;
                    // Add details
                    bookCart.push({
                        id: book.id,
                        title: book.title,
                        author: book.author,
                        call_number: book.call_number,
                        accession_no: book.accession_no,
                        barcode: book.barcode,
                        book_section: book_section,
                        borrow_period: borrow_period
                    });
                    barcodeInput.value = '';
                    renderCart();
                    barcodeInput.focus();
                } else {
                    showAlert(result.message || 'Book is invalid or not available.', 'error');
                    barcodeInput.select();
                }
            } catch (err) {
                showAlert('Error verifying book barcode.', 'error');
            }
        }

        function renderCart() {
            const cartItems = document.getElementById('cartItems');
            const cartSection = document.getElementById('cartSection');
            const cartCount = document.getElementById('cartCount');
            const submitBtn = document.getElementById('submitBtn');

            if (bookCart.length === 0) {
                cartSection.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Process Borrowing';
            } else {
                cartSection.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.innerHTML = `Process Borrowing (${bookCart.length})`;
                
                cartCount.textContent = bookCart.length;
                cartItems.innerHTML = '';

                bookCart.forEach((book, index) => {
                    const item = document.createElement('div');
                    item.className = 'p-3 flex items-center justify-between gap-4 border-b border-gray-100 last:border-0';
                    item.innerHTML = `
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-800 text-sm truncate">${book.title}</p>
                            <p class="text-xs text-gray-400">${book.author || 'No Author'} &bull; Accession: ${book.accession_no}</p>
                            <div class="flex gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">${book.book_section}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">${book.borrow_period}</span>
                            </div>
                        </div>
                        <button type="button" onclick="removeBookFromCart(${index})" class="text-gray-400 hover:text-red-600 transition p-1">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    `;
                    cartItems.appendChild(item);
                });
            }
        }

        function removeBookFromCart(index) {
            bookCart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            bookCart = [];
            renderCart();
        }

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

        function getFineText(section) {
            if (section === 'Reserved') {
                return 'Fine: Php 5.00 per hour overdue.';
            } else {
                return 'Fine: Php 5.00 per day overdue (including Saturday).';
            }
        }

        function fillReceipt(result) {
            const now = new Date();

            // Preview panel
            document.getElementById('r-date').textContent = now.toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            document.getElementById('r-name').textContent = result.borrower_name;
            document.getElementById('r-borrow-type').textContent = result.borrow_type;
            document.getElementById('r-borrower-id').textContent = result.borrower_id;

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

            // Generate book rows for preview
            const rBooksList = document.getElementById('r-books-list');
            rBooksList.innerHTML = '';

            result.books.forEach(book => {
                const bookEl = document.createElement('div');
                bookEl.className = 'bg-gray-50 rounded-lg p-3 space-y-1 text-xs border border-gray-100';
                
                const fineText = getFineText(book.book_section);

                bookEl.innerHTML = `
                    <p class="font-bold text-gray-800 text-sm truncate">${book.title}</p>
                    <p class="text-gray-500"><span class="font-semibold text-gray-700">Author:</span> ${book.author || 'No Author'}</p>
                    <p class="text-gray-500"><span class="font-semibold text-gray-700">Accession:</span> ${book.accession_no} &bull; <span class="font-semibold text-gray-700">Call No:</span> ${book.call_number}</p>
                    <p class="text-gray-500"><span class="font-semibold text-gray-700">Section:</span> ${book.book_section}</p>
                    <p class="text-amber-800 font-semibold bg-amber-50 rounded p-1.5 border border-amber-100 flex justify-between items-center mt-1">
                        <span>Due: ${fmtFull(book.due_date)}</span>
                    </p>
                    <p class="text-emerald-700 font-bold mt-1">${fineText}</p>
                `;
                rBooksList.appendChild(bookEl);
            });

            // Print area
            document.getElementById('p-date').textContent = now.toLocaleString('en-PH');
            document.getElementById('p-borrowed').textContent = now.toLocaleString('en-PH');
            document.getElementById('p-name').textContent = result.borrower_name;
            document.getElementById('p-borrow-type').textContent = result.borrow_type;
            document.getElementById('p-borrower-id').textContent = result.borrower_id;
            const pCyWrap = document.getElementById('p-course-year-wrap');
            if (result.borrower_year || result.borrower_course) {
                document.getElementById('p-course').textContent = result.borrower_course || '';
                document.getElementById('p-year').textContent = result.borrower_year || '';
                pCyWrap.style.display = '';
            } else {
                pCyWrap.style.display = 'none';
            }

            // Generate book rows for print
            const pBooksContainer = document.getElementById('p-books-container');
            pBooksContainer.innerHTML = '';

            let sectionsList = [];

            result.books.forEach((book, idx) => {
                sectionsList.push(book.book_section);
                const bookEl = document.createElement('div');
                bookEl.style.marginBottom = '4px';
                bookEl.style.paddingBottom = '4px';
                bookEl.style.borderBottom = idx < result.books.length - 1 ? '1px dashed #000' : 'none';
                
                bookEl.innerHTML = `
                    <div style="font-weight: bold; font-size: 11px;">${idx + 1}. ${book.title}</div>
                    <div style="font-size: 9px; margin-top: 1px; line-height: 1.2;">
                        Acc: ${book.accession_no} &bull; Call: ${book.call_number}<br>
                        Due: ${fmtFull(book.due_date)}<br>
                        Section: ${book.book_section}<br>
                        Rate: ${getFineText(book.book_section)}
                    </div>
                `;
                pBooksContainer.appendChild(bookEl);
            });

            // Generate unique fine rules summary
            const uniqueSections = [...new Set(sectionsList)];
            const fineSummaryText = uniqueSections.map(sec => {
                if (sec === 'Reserved') {
                    return 'Reserve Books: Php 5.00/hour';
                }
                return `${sec} Books: Php 5.00/day (incl. Sat)`;
            }).join(' | ');

            document.getElementById('p-fine-rules-summary').textContent = `Fine Rates: ${fineSummaryText}`;

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
            const borrow_type = document.getElementById('borrow_type').value;

            if (bookCart.length === 0) {
                showAlert('Your cart is empty. Please add at least one book.', 'error');
                return;
            }

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
                        borrow_type,
                        books: bookCart
                    })
                });
                const result = await res.json();

                if (result.success) {
                    fillReceipt(result);
                    showAlert(result.message || 'Books borrowed successfully.');
                    clearCart();
                    document.getElementById('borrower_id').value = '';
                    document.getElementById('borrower_id').focus();
                } else {
                    showAlert(result.message || 'Operation failed', 'error');
                }
            } catch (err) {
                showAlert('A network error occurred.', 'error');
            } finally {
                submitBtn.disabled = false;
                renderCart();
            }
        }
    </script>
@endsection
