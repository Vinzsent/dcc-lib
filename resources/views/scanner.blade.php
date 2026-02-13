<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCC Library Scanner</title>
    <link rel="icon" type="image/png" href="{{ asset('images/DCC2.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .scanner-bg {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
        }
        .status-badge {
            transition: all 0.2s ease;
        }
        @keyframes scan-flash {
            0% { transform: scale(1); filter: brightness(1); }
            50% { transform: scale(1.02); filter: brightness(1.2); }
            100% { transform: scale(1); filter: brightness(1); }
        }
        .scan-pulse {
            animation: scan-flash 0.3s ease-out;
        }
    </style>
</head>
<body class="scanner-bg min-h-screen flex items-center justify-center p-4">

    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[500px]">
        <!-- Left Side: Scanner Input Area -->
        <div class="md:w-1/2 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100">
            <div class="text-center mb-8">
                <img src="{{ asset('images/DCC2.png') }}" alt="DCC Logo" class="h-20 mx-auto mb-4 opacity-80" onerror="this.src='/logo.png'; this.classList.remove('grayscale')">
                <h1 class="text-2xl font-bold text-gray-800">Library Attendance</h1>
                <p class="text-gray-500 text-sm mt-1">Please tap or scan your Student ID</p>
            </div>

            <!-- Invisible/Auto-focused Input -->
            <div class="w-full max-w-xs relative">
                <input type="text" id="sid_input" autocomplete="off" 
                    class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-xl text-center text-xl font-bold tracking-widest focus:outline-none focus:border-emerald-600 transition-all uppercase"
                    placeholder="SCAN ID HERE">
                
                <div id="loading_spinner" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-6 w-6 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <div class="mt-8 text-center">
                <div id="real_time" class="text-3xl font-bold text-gray-700">00:00:00</div>
                <div id="real_date" class="text-gray-400 text-sm uppercase tracking-widest font-semibold mt-1">Saturday, Jan 31, 2026</div>
                
                <div class="mt-8 grid grid-cols-3 gap-4 w-full">
                    <div class="bg-blue-50 p-3 rounded-lg text-center">
                        <div id="stat_inside" class="text-2xl font-bold text-blue-700">{{ $studentsInside }}</div>
                        <div class="text-xs text-blue-500 font-semibold uppercase">Inside</div>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-lg text-center">
                        <div id="stat_in" class="text-2xl font-bold text-emerald-700">{{ $totalTimeIn }}</div>
                        <div class="text-xs text-emerald-500 font-semibold uppercase">Time In</div>
                    </div>
                    <div class="bg-orange-50 p-3 rounded-lg text-center">
                        <div id="stat_out" class="text-2xl font-bold text-orange-700">{{ $totalTimeOut }}</div>
                        <div class="text-xs text-orange-500 font-semibold uppercase">Time Out</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Feedback Area -->
        <div id="feedback_panel" class="md:w-1/2 bg-gray-50 p-8 flex flex-col items-center justify-center text-center relative overflow-hidden transition-all duration-200">
            <div id="welcome_message" class="transition-all duration-200 opacity-100">
                 <div class="w-32 h-32 rounded-full bg-emerald-100 flex items-center justify-center mb-6 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Scanner Ready</h2>
                <p class="text-gray-500 max-w-xs mx-auto mt-2">The system is ready to accept student library attendance logs.</p>
            </div>

            <!-- Student Content (Hidden by default) -->
            <div id="student_display" class="hidden transition-all duration-200 transform translate-y-4 opacity-0 w-full">
                <!-- Profile Image -->
                <div id="student_profile_container" class="mb-6">
                    <img id="student_profile" src="" alt="Profile" class="w-40 h-40 rounded-full mx-auto object-cover border-4 border-white shadow-lg hidden">
                    <div id="student_initials" class="w-40 h-40 rounded-full mx-auto flex items-center justify-center text-4xl font-bold text-white shadow-lg"></div>
                </div>

                <div id="status_badge" class="inline-block px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4 status-badge"></div>

                <h2 id="student_name" class="text-3xl font-extrabold text-gray-800"></h2>
                <p id="student_detail" class="text-emerald-700 font-semibold mb-2"></p>
                <p id="student_sid" class="text-gray-400 text-lg"></p>

                <div class="mt-8 p-4 bg-white rounded-xl shadow-sm inline-block">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-widest mb-1">Last Transaction</p>
                    <p id="transaction_time" class="text-2xl font-bold text-gray-800"></p>
                </div>
            </div>

            <!-- Error Message -->
            <div id="error_message" class="hidden flex flex-col items-center">
                 <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <p id="error_text" class="text-red-600 font-bold text-lg"></p>
            </div>
        </div>
    </div>

    <!-- Admin Link -->
    <a href="{{ url('/login') }}" class="fixed bottom-4 right-4 text-emerald-100/50 hover:text-white transition text-xs font-bold uppercase tracking-widest flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        Admin Portal
    </a>

    <script>
        const sidInput = document.getElementById('sid_input');
        const spinner = document.getElementById('loading_spinner');
        const welcomeMsg = document.getElementById('welcome_message');
        const studentDisplay = document.getElementById('student_display');
        const errorMsg = document.getElementById('error_message');
        const errorText = document.getElementById('error_text');

        // Keep input focused
        document.addEventListener('click', () => sidInput.focus());
        window.addEventListener('load', () => sidInput.focus());

        // Handle scan
        sidInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const sid = this.value.trim();
                if (sid) {
                    performScan(sid);
                }
                this.value = '';
            }
        });

        async function performScan(sid) {
            spinner.classList.remove('hidden');
            
            try {
                const response = await fetch("{{ route('scan') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ sid: sid })
                });

                const data = await response.json();

                if (data.success) {
                    showStudent(data);
                } else {
                    showError(data.message || 'Scan failed.');
                }
            } catch (err) {
                showError('Network error. Please check connection.');
            } finally {
                spinner.classList.add('hidden');
            }
        }

        function showStudent(data) {
            // Instant feedback flash
            const panel = document.getElementById('feedback_panel');
            panel.classList.remove('scan-pulse');
            void panel.offsetWidth; // Force reflow
            panel.classList.add('scan-pulse');

            // Reset views
            welcomeMsg.classList.add('hidden');
            errorMsg.classList.add('hidden');
            studentDisplay.classList.remove('hidden');
            
            // Animation reset
            studentDisplay.classList.remove('translate-y-4', 'opacity-0');
            studentDisplay.style.opacity = '1';

            const s = data.student;
            document.getElementById('student_name').innerText = `${s.firstname} ${s.lastname}`;
            document.getElementById('student_detail').innerText = `${s.department} | ${s.course} ${s.section} - ${s.year}`;
            document.getElementById('student_sid').innerText = s.sid;
            document.getElementById('transaction_time').innerText = data.time;

            // Status Badge
            const badge = document.getElementById('status_badge');
            badge.innerText = data.status === 'in' ? 'Time In' : 'Time Out';
            badge.className = `inline-block px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4 status-badge ${data.status === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}`;

            // Update Stats
            if (data.counts) {
                document.getElementById('stat_inside').innerText = data.counts.inside;
                document.getElementById('stat_in').innerText = data.counts.in;
                document.getElementById('stat_out').innerText = data.counts.out;
            }

            // Profile
            const profileImg = document.getElementById('student_profile');
            const initialsDiv = document.getElementById('student_initials');
            
            if (s.profile) {
                profileImg.src = s.profile;
                profileImg.classList.remove('hidden');
                initialsDiv.classList.add('hidden');
            } else {
                profileImg.classList.add('hidden');
                initialsDiv.classList.remove('hidden');
                initialsDiv.innerText = s.firstname[0] + s.lastname[0];
                initialsDiv.style.backgroundColor = data.status === 'in' ? '#059669' : '#dc2626';
            }

            // Faster auto reset (2 seconds instead of 5)
            clearTimeout(window.scanTimeout);
            window.scanTimeout = setTimeout(resetUI, 2000);
        }

        function showError(msg) {
            // Flash red for errors
            const panel = document.getElementById('feedback_panel');
            panel.classList.add('bg-red-50');
            setTimeout(() => panel.classList.remove('bg-red-50'), 300);

            welcomeMsg.classList.add('hidden');
            studentDisplay.classList.add('hidden');
            errorMsg.classList.remove('hidden');
            errorText.innerText = msg;

            clearTimeout(window.scanTimeout);
            window.scanTimeout = setTimeout(resetUI, 2000);
        }

        function resetUI() {
            welcomeMsg.classList.remove('hidden');
            studentDisplay.classList.add('hidden');
            errorMsg.classList.add('hidden');
            
            // Reset animation state
            studentDisplay.classList.add('translate-y-4', 'opacity-0');
            studentDisplay.style.opacity = '0';
        }

        // Live Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('real_time').innerText = now.toLocaleTimeString([], { hour12: false });
            document.getElementById('real_date').innerText = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
