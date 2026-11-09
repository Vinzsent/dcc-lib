@extends('layouts.app')

@section('title', 'Borrow Checker')
@section('header', 'Library - Borrow Checker')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ─── Search Hero ─── --}}
    <div class="rounded-2xl shadow-lg mb-6"
         style="background: linear-gradient(135deg, #073b1d 0%, #0a4d2a 55%, #156d3f 100%);">
        <div class="px-6 pt-8 pb-8 text-center relative">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center mb-4 ring-1 ring-white/20">
                <svg class="w-7 h-7 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-white">Student Borrow Checker</h2>
            <p class="text-emerald-100/80 mt-2 text-sm md:text-base">
                Search a student to see if he or she currently borrows a book — and view the full borrow history.
            </p>

            <div class="relative max-w-2xl mx-auto mt-6" id="searchWrap">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="studentSearch" autocomplete="off" autofocus
                           placeholder="Type name, Student ID (SID) or RFID…"
                           class="w-full pl-12 pr-12 py-4 rounded-xl border-0 shadow-xl text-gray-800 text-base md:text-lg bg-white placeholder-gray-400 focus:ring-4 focus:ring-amber-300/50 focus:outline-none transition" />
                    <div id="searchSpinner" class="absolute inset-y-0 right-0 pr-4 hidden items-center">
                        <svg class="animate-spin w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Live suggestions dropdown --}}
                <div id="searchResults"
                     class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 z-30 hidden text-left">
                </div>
            </div>

            <p class="text-emerald-100/60 text-xs mt-3">Results appear as you type · Use ↑ ↓ and Enter to select</p>
        </div>
    </div>

    {{-- ─── Alert ─── --}}
    <div id="alertBox" class="hidden mb-6 rounded-xl border px-4 py-3 text-sm flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.732-2.5l-7-12a2 2 0 00-3.464 0l-7 12A2 2 0 005 19z" />
        </svg>
        <span id="alertText"></span>
    </div>

    {{-- ─── Idle State ─── --}}
    <div id="idleState" class="card bg-white rounded-2xl text-center py-16 px-6">
        <div class="mx-auto w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-emerald-600/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 21h16v-11H4v11z" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">No student selected yet</h3>
        <p class="text-gray-400 text-sm mt-1">Start typing a student's name or ID in the search box above to check their borrow records.</p>
    </div>

    {{-- ─── Result ─── --}}
    <div id="resultSection" class="hidden space-y-6">

        {{-- Profile card --}}
        <div class="card bg-white rounded-2xl overflow-hidden p-0">
            <div class="flex flex-col md:flex-row md:items-center gap-5 p-6">
                <div id="studentAvatar"
                     class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0 shadow-md"
                     style="background: linear-gradient(135deg, #0a4d2a, #156d3f);"></div>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 id="studentName" class="text-xl font-bold text-gray-800"></h3>
                        <span id="studentStatusPill"></span>
                    </div>
                    <div id="studentMeta" class="flex flex-wrap gap-2 mt-2"></div>
                </div>

                <div class="text-right flex-shrink-0">
                    <div class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Student ID</div>
                    <div id="studentSid" class="text-lg font-mono font-bold text-gray-700"></div>
                    <div id="studentRfid" class="text-xs text-gray-400 mt-0.5"></div>
                </div>
            </div>

            {{-- Stat chips --}}
            <div class="grid grid-cols-2 md:grid-cols-5 border-t border-gray-100 divide-x divide-gray-100">
                <div class="p-4 text-center bg-gray-50/50">
                    <div id="statTotal" class="text-2xl font-bold text-gray-800">0</div>
                    <div class="text-xs text-gray-500 mt-1 font-medium uppercase tracking-wide">Total Borrows</div>
                </div>
                <div class="p-4 text-center bg-blue-50/50">
                    <div id="statActive" class="text-2xl font-bold text-blue-700">0</div>
                    <div class="text-xs text-blue-600/70 mt-1 font-medium uppercase tracking-wide">Currently Borrowed</div>
                </div>
                <div class="p-4 text-center bg-emerald-50/50">
                    <div id="statReturned" class="text-2xl font-bold text-emerald-700">0</div>
                    <div class="text-xs text-emerald-600/70 mt-1 font-medium uppercase tracking-wide">Returned</div>
                </div>
                <div class="p-4 text-center bg-red-50/50">
                    <div id="statOverdue" class="text-2xl font-bold text-red-600">0</div>
                    <div class="text-xs text-red-500/70 mt-1 font-medium uppercase tracking-wide">Overdue</div>
                </div>
                <div class="p-4 text-center bg-amber-50/50 col-span-2 md:col-span-1">
                    <div id="statFines" class="text-2xl font-bold text-amber-600">₱0.00</div>
                    <div class="text-xs text-amber-600/70 mt-1 font-medium uppercase tracking-wide">Total Fines</div>
                </div>
            </div>
        </div>

        {{-- History table --}}
        <div class="card bg-white rounded-2xl p-6" id="historyCard">
            <div class="flex flex-col md:flex-row justify-between items-center mb-5 gap-3">
                <div class="flex items-center gap-3">
                    <h3 class="text-gray-700 font-bold text-lg">Borrow History</h3>
                    <span id="historyCount"
                          class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">0</span>
                </div>
                <button type="button" onclick="refreshHistory()"
                        class="inline-flex items-center gap-2 text-sm font-medium text-emerald-800 hover:text-emerald-600 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-left border-collapse min-w-[820px]">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-xs leading-normal">
                            <th class="py-3 px-4">Book</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Section</th>
                            @if($showCampus)
                                <th class="py-3 px-4">Campus</th>
                            @endif
                            <th class="py-3 px-4">Date Borrowed</th>
                            <th class="py-3 px-4">Due Date</th>
                            <th class="py-3 px-4">Returned On</th>
                            <th class="py-3 px-4 text-center">Fine</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody" class="text-gray-600 text-sm font-light">
                    </tbody>
                </table>
            </div>
            <div id="historyEmpty" class="hidden text-center py-14">
                <div class="mx-auto w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h4 class="text-gray-600 font-semibold">No borrow records found</h4>
                <p class="text-gray-400 text-sm mt-1">This student has never borrowed a book
                    @if(!$showCampus) for your collection @endif.</p>
            </div>
            <div id="historyLoading" class="hidden text-center py-14">
                <svg class="animate-spin w-8 h-8 mx-auto text-emerald-700" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <p class="text-gray-400 text-sm mt-3">Loading borrow history…</p>
            </div>

            {{-- Pagination --}}
            <div id="historyPagination" class="hidden mt-5 border-t border-gray-100 pt-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-3">
                    <div id="historyMeta" class="text-sm text-gray-500"></div>
                    <div id="historyPager" class="flex items-center gap-1 flex-wrap justify-center"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const searchInput    = document.getElementById('studentSearch');
    const resultsBox     = document.getElementById('searchResults');
    const searchSpinner  = document.getElementById('searchSpinner');
    const idleState      = document.getElementById('idleState');
    const resultSection  = document.getElementById('resultSection');
    const alertBox       = document.getElementById('alertBox');
    const alertText      = document.getElementById('alertText');

    const showCampus     = @json($showCampus);

    let debounceTimer  = null;
    let abortCtrl      = null;
    let suggestions    = [];
    let activeIndex    = -1;
    let currentStudent = null;
    let currentPage    = 1;

    const esc = (v) => String(v ?? '').replace(/[&<>"']/g, m => ({'&':'&','<':'<','>':'>','"':'"',"'":'&#39;'}[m]));

    // ───────────────────────── Search ─────────────────────────
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(doSearch, 250);
    });

    searchInput.addEventListener('keydown', (e) => {
        if (resultsBox.classList.contains('hidden') || suggestions.length === 0) return;

        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = e.key === 'ArrowDown'
                ? (activeIndex + 1) % suggestions.length
                : (activeIndex - 1 + suggestions.length) % suggestions.length;
            highlightSuggestion();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            selectStudent(suggestions[activeIndex >= 0 ? activeIndex : 0]);
        } else if (e.key === 'Escape') {
            hideResults();
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#searchWrap')) hideResults();
    });

    function doSearch() {
        const q = searchInput.value.trim();
        if (!q) { hideResults(); return; }

        if (abortCtrl) abortCtrl.abort();
        abortCtrl = new AbortController();

        searchSpinner.classList.remove('hidden');
        searchSpinner.classList.add('flex');

        fetch(`{{ route('admin.library.checker.students') }}?q=${encodeURIComponent(q)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            signal: abortCtrl.signal
        })
        .then(r => r.json())
        .then(data => renderResults(data.students || [], data.total || 0))
        .catch(err => { if (err.name !== 'AbortError') hideResults(); })
        .finally(() => {
            searchSpinner.classList.add('hidden');
            searchSpinner.classList.remove('flex');
        });
    }

    function renderResults(students, total = students.length) {
        suggestions = students;
        activeIndex = -1;

        if (students.length === 0) {
            resultsBox.innerHTML = `
                <div class="px-5 py-6 text-center text-gray-400 text-sm">
                    No student found matching "<span class="font-semibold text-gray-500">${esc(searchInput.value.trim())}</span>"
                </div>`;
            resultsBox.classList.remove('hidden');
            return;
        }

        const rows = students.map((s, i) => {
            const badge = s.active_borrows > 0
                ? `<span class="ml-auto flex-shrink-0 bg-amber-100 text-amber-700 text-xs font-semibold px-2.5 py-1 rounded-full">${s.active_borrows} borrowed now</span>`
                : (s.total_borrows > 0
                    ? `<span class="ml-auto flex-shrink-0 bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-full">${s.total_borrows} total</span>`
                    : `<span class="ml-auto flex-shrink-0 bg-emerald-50 text-emerald-600 text-xs font-medium px-2.5 py-1 rounded-full">No borrows yet</span>`);

            const sub = [s.course, s.grade ? 'Grade ' + s.grade : null, s.section, s.department]
                .filter(Boolean).map(esc).join(' · ');

            return `
                <button type="button" data-index="${i}" onclick="selectStudent(suggestions[${i}])"
                        class="suggestion w-full flex items-center gap-3 px-4 py-3 hover:bg-emerald-50/60 transition text-left border-b border-gray-50 last:border-0">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                         style="background: linear-gradient(135deg, #0a4d2a, #156d3f);">${initials(s.name)}</div>
                    <div class="min-w-0">
                        <div class="font-semibold text-gray-800 text-sm truncate">${esc(s.name)}</div>
                        <div class="text-xs text-gray-400 truncate">${esc(s.sid)}${sub ? ' — ' + sub : ''}</div>
                    </div>
                    ${badge}
                </button>`;
        }).join('');

        const footer = total > students.length
            ? `<div class="px-4 py-2.5 bg-gray-50 text-xs text-gray-500 text-center">
                 Showing <span class="font-semibold text-gray-600">${students.length}</span> of <span class="font-semibold text-gray-600">${total}</span> matches — keep typing to narrow down
               </div>`
            : '';

        resultsBox.innerHTML = `<div class="max-h-80 overflow-y-auto rounded-t-xl">${rows}</div>${footer}`;
        resultsBox.classList.remove('hidden');
    }

    function highlightSuggestion() {
        resultsBox.querySelectorAll('.suggestion').forEach((el, i) => {
            el.classList.toggle('bg-emerald-50', i === activeIndex);
        });
    }

    function hideResults() {
        resultsBox.classList.add('hidden');
        suggestions = [];
        activeIndex = -1;
    }

    // ───────────────────────── History ─────────────────────────
    function selectStudent(student) {
        if (!student) return;
        hideResults();
        searchInput.value = `${student.name} (${student.sid})`;
        searchInput.blur();

        currentStudent = { ...student };
        renderProfile(student, null);

        idleState.classList.add('hidden');
        resultSection.classList.remove('hidden');
        resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

        loadHistory(student.sid, 1);
    }

    function refreshHistory() {
        if (currentStudent) loadHistory(currentStudent.sid, currentPage);
    }

    function goToPage(page) {
        if (currentStudent) loadHistory(currentStudent.sid, page);
    }

    function loadHistory(sid, page = 1) {
        currentPage = page;
        setLoading(true);
        hideAlert();

        fetch(`{{ route('admin.library.checker.history') }}?sid=${encodeURIComponent(sid)}&page=${page}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async (r) => {
            const data = await r.json();
            if (!r.ok || !data.success) throw new Error(data.message || 'Unable to load borrow history.');
            renderProfile(data.student, data.summary);
            renderHistory(data.transactions, data.summary, data.pagination);
        })
        .catch(err => showAlert(err.message))
        .finally(() => setLoading(false));
    }

    function renderProfile(s, summary) {
        document.getElementById('studentAvatar').textContent = initials(s.name);
        document.getElementById('studentName').textContent   = s.name;
        document.getElementById('studentSid').textContent    = s.sid;
        document.getElementById('studentRfid').textContent   = s.rfid ? `RFID: ${s.rfid}` : '';

        // Meta chips
        const chips = [
            s.course  ? `📚 ${s.course}` : null,
            s.year    ? `🗓 Year ${s.year}` : null,
            s.grade   ? `🎓 Grade ${s.grade}` : null,
            s.section ? `Section ${s.section}` : null,
            s.department ? `🏛 ${s.department}` : null,
            s.campus  ? `📍 ${s.campus}` : null,
        ].filter(Boolean);

        document.getElementById('studentMeta').innerHTML = chips.map(c =>
            `<span class="bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-1 rounded-lg">${esc(c)}</span>`
        ).join('');

        // Status pill
        const pill = document.getElementById('studentStatusPill');
        if (summary) {
            if (summary.currently_borrowing > 0) {
                pill.innerHTML = `<span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Borrowing ${summary.currently_borrowing} book${summary.currently_borrowing > 1 ? 's' : ''} now
                </span>`;
            } else if (summary.has_borrowed) {
                pill.innerHTML = `<span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1.5 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    No books borrowed at present
                </span>`;
            } else {
                pill.innerHTML = `<span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1.5 rounded-full">
                    Never borrowed a book
                </span>`;
            }
        } else {
            pill.innerHTML = '';
        }

        // Stats
        if (summary) {
            document.getElementById('statTotal').textContent    = summary.total_borrows;
            document.getElementById('statActive').textContent   = summary.currently_borrowing;
            document.getElementById('statReturned').textContent = summary.total_returned;
            document.getElementById('statOverdue').textContent  = summary.total_overdue;
            document.getElementById('statFines').textContent    = '₱' + Number(summary.total_fines).toFixed(2);
        }
    }

    function renderHistory(transactions, summary, pagination = null) {
        const body  = document.getElementById('historyBody');
        const empty = document.getElementById('historyEmpty');
        const count = document.getElementById('historyCount');
        const table = document.querySelector('#historyCard table');

        count.textContent = `${summary.total_borrows} record${summary.total_borrows === 1 ? '' : 's'}`;
        renderPager(pagination);

        if (transactions.length === 0) {
            table.closest('.overflow-x-auto').classList.add('hidden');
            empty.classList.remove('hidden');
            body.innerHTML = '';
            return;
        }

        empty.classList.add('hidden');
        table.closest('.overflow-x-auto').classList.remove('hidden');

        body.innerHTML = transactions.map(t => `
            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/70 transition">
                <td class="py-3.5 px-4">
                    <div class="font-medium text-gray-800">${esc(t.title)}</div>
                    <div class="text-xs text-gray-400">#${esc(t.accession_no)}${t.author ? ' · ' + esc(t.author) : ''}</div>
                </td>
                <td class="py-3.5 px-4 whitespace-nowrap">${esc(t.borrow_type ?? '-')}</td>
                <td class="py-3.5 px-4 whitespace-nowrap">${esc(t.book_section ?? '-')}</td>
                ${showCampus ? `<td class="py-3.5 px-4 whitespace-nowrap text-purple-700 font-medium">${esc(t.campus ?? 'N/A')}</td>` : ''}
                <td class="py-3.5 px-4 whitespace-nowrap text-gray-500">${esc(t.date_borrowed ?? '-')}</td>
                <td class="py-3.5 px-4 whitespace-nowrap text-gray-500">${esc(t.due_date ?? '-')}</td>
                <td class="py-3.5 px-4 whitespace-nowrap text-gray-500">${esc(t.date_returned ?? '—')}</td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap font-semibold ${t.fine > 0 ? 'text-red-600' : 'text-gray-300'}">
                    ${t.fine > 0 ? '₱' + Number(t.fine).toFixed(2) : '—'}
                </td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">${statusBadge(t.status)}</td>
            </tr>`).join('');
    }

    function renderPager(p) {
        const wrap = document.getElementById('historyPagination');
        const meta = document.getElementById('historyMeta');
        const pager = document.getElementById('historyPager');

        if (!p || p.last_page <= 1) {
            wrap.classList.add('hidden');
            pager.innerHTML = '';
            meta.textContent = '';
            return;
        }

        wrap.classList.remove('hidden');
        meta.innerHTML = `Showing <span class="font-semibold text-gray-700">${p.from}</span>–<span class="font-semibold text-gray-700">${p.to}</span> of <span class="font-semibold text-gray-700">${p.total}</span> records`;

        // Build page list with ellipses: 1 … (c-1) c (c+1) … last
        const items = [];
        for (let i = 1; i <= p.last_page; i++) {
            if (i === 1 || i === p.last_page || Math.abs(i - p.current_page) <= 1) {
                items.push(i);
            } else if (items[items.length - 1] !== '…') {
                items.push('…');
            }
        }

        pager.innerHTML =
            pageBtn('‹', p.current_page - 1, p.current_page === 1) +
            items.map(it => it === '…'
                ? `<span class="px-1.5 text-gray-400 text-sm select-none">…</span>`
                : pageBtn(it, it, false, it === p.current_page)
            ).join('') +
            pageBtn('›', p.current_page + 1, p.current_page === p.last_page);
    }

    function pageBtn(label, page, disabled, active = false) {
        const base = 'min-w-[36px] h-9 px-2.5 inline-flex items-center justify-center rounded-lg text-sm font-medium border transition';
        if (active) {
            return `<button type="button" class="${base} text-white border-transparent shadow" style="background-color: var(--color-primary);">${label}</button>`;
        }
        if (disabled) {
            return `<button type="button" disabled class="${base} text-gray-300 border-gray-100 bg-gray-50 cursor-not-allowed">${label}</button>`;
        }
        return `<button type="button" onclick="goToPage(${page})" class="${base} text-gray-600 border-gray-200 bg-white hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-800">${label}</button>`;
    }

    function statusBadge(status) {
        if (status === 'Returned') {
            return `<span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 py-1 px-3 rounded-full text-xs font-semibold">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                Returned</span>`;
        }
        if (status === 'Overdue') {
            return `<span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                Overdue · Not Returned</span>`;
        }
        return `<span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 py-1 px-3 rounded-full text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Borrowed · Not Returned</span>`;
    }

    function setLoading(on) {
        const loading = document.getElementById('historyLoading');
        const table   = document.querySelector('#historyCard table');
        const empty   = document.getElementById('historyEmpty');

        if (on) {
            loading.classList.remove('hidden');
            table.closest('.overflow-x-auto').classList.add('hidden');
            empty.classList.add('hidden');
        } else {
            loading.classList.add('hidden');
        }
    }

    function showAlert(message) {
        alertText.textContent = message || 'Something went wrong.';
        alertBox.classList.remove('hidden');
        alertBox.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
    }

    function hideAlert() {
        alertBox.classList.add('hidden');
    }

    function initials(name) {
        return String(name || '?').trim().split(/\s+/).slice(0, 2).map(w => w[0].toUpperCase()).join('');
    }
</script>
@endsection
