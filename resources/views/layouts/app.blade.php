<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - DCC Library</title>
    <link rel="icon" type="image/png" href="{{ asset('images/DCC2.png') }}">
    
    <!-- Using Vanilla CSS with Variables for Theme consistency -->
    <style>
        :root {
            /* Palette specific to DCC Library (Green Theme) */
            --color-primary: #0a4d2a;       /* Main Brand Color */
            --color-primary-dark: #073b1d;  /* Darker shade */
            --color-primary-light: #156d3f; /* Lighter tint */
            --color-accent: #fbbf24;        /* Warning/Accent if needed */
            
            --color-bg-body: #f3f4f6;       /* Light Gray Background */
            --color-bg-sidebar: #073b1d;    /* Sidebar Background (Dark Green) */
            --color-bg-header: #ffffff;     /* Header White */
            --color-bg-card: #ffffff;       /* Card White */
            
            --color-text-main: #1f2937;     /* Dark Text */
            --color-text-muted: #6b7280;    /* Gray Text */
            --color-text-sidebar: #e5e7eb;  /* Light Text for Sidebar */
            --color-text-sidebar-active: #ffffff; 
            
            --color-border: #e5e7eb;
            
            --sidebar-width: 260px;
            --sidebar-width-collapsed: 64px;
            --header-height: 64px;
            
            --transition-speed: 0.3s;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        
        body {
            background-color: var(--color-bg-body);
            color: var(--color-text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--color-bg-sidebar);
            color: var(--color-text-sidebar);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 50;
            transition: width var(--transition-speed) ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1rem;
            background-color: rgba(0,0,0,0.2); /* Darken slightly */
            white-space: nowrap;
        }

        .brand-logo {
            font-weight: bold;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            overflow: hidden;
        }
        
        .brand-icon {
            min-width: 32px;
            display: flex;
            justify-content: center;
            margin-right: 0.75rem;
        }

        .nav-list {
            list-style: none;
            margin-top: 1rem;
            flex-grow: 1;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--color-text-sidebar);
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
            white-space: nowrap;
            border-left: 4px solid transparent;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link.active {
            background-color: rgba(255,255,255,0.15);
            color: var(--color-text-sidebar-active);
            border-left-color: var(--color-accent);
        }

        .nav-icon {
            min-width: 24px;
            height: 24px;
            margin-right: 1rem;
        }

        /* Toggle State: Collapsed (Desktop) */
        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-width-collapsed);
        }

        body.sidebar-collapsed .brand-text,
        body.sidebar-collapsed .link-text {
            opacity: 0;
            pointer-events: none;
            position: absolute; /* Hide properly */
        }
        
        body.sidebar-collapsed .sidebar-header {
            justify-content: center;
            padding: 0;
        }
        
        body.sidebar-collapsed .brand-icon {
             margin-right: 0;
        }
        
        body.sidebar-collapsed .nav-link {
            justify-content: center;
            padding: 0.75rem 0;
        }
        
        body.sidebar-collapsed .nav-icon {
            margin-right: 0;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed) ease;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        body.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-width-collapsed);
        }

        /* Header */
        header {
            height: var(--header-height);
            background-color: var(--color-bg-header);
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .header-left { display: flex; align-items: center; gap: 1rem; }
        
        .toggle-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--color-text-muted);
            padding: 0.5rem;
            border-radius: 5px;
            display: flex;
        }
        
        .toggle-btn:hover { background-color: #f3f4f6; color: var(--color-primary); }

        .page-title { font-size: 1.25rem; font-weight: 600; color: var(--color-text-main); }
        
        .content-body {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Utility */
        .card {
            background: var(--color-bg-card);
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            border: 1px solid var(--color-border);
        }
        
        /* Mobile Overlay Behavior */
        .mobile-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 45;
        }

        @media (max-width: 1024px) {
            :root {
                --sidebar-width: 260px; /* Full width on mobile when open */
            }

            .sidebar {
                transform: translateX(-100%); /* Hidden by default */
                width: var(--sidebar-width); /* Fixed width */
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }
            
            body.sidebar-open .mobile-backdrop {
                display: block;
            }

            .main-content, 
            body.sidebar-collapsed .main-content {
                margin-left: 0 !important; /* No margin on mobile */
            }
            
            /* Disable collapsed state logic for mobile, we use open/closed */
            body.sidebar-collapsed .sidebar {
                 width: var(--sidebar-width); /* Reset width overrides */
            }
            body.sidebar-collapsed .brand-text,
            body.sidebar-collapsed .link-text {
                opacity: 1;
                position: static;
                pointer-events: auto;
            }
             body.sidebar-collapsed .sidebar-header {
                justify-content: flex-start;
                padding: 0 1rem;
            }
            body.sidebar-collapsed .nav-link {
                justify-content: flex-start;
                padding: 0.75rem 1rem;
            }
            body.sidebar-collapsed .nav-icon {
                margin-right: 1rem;
            }
        }

        /* Custom Pagination Styling */
        .pagination-container nav {
            display: flex;
            justify-content: center;
        }
        .pagination-container .relative.inline-flex.items-center.px-4.py-2 {
            border-color: var(--color-border);
            color: var(--color-text-main);
            transition: all 0.2s;
        }
        .pagination-container .relative.inline-flex.items-center.px-4.py-2:hover {
            background-color: #f9fafb;
            color: var(--color-primary);
        }
        .pagination-container [aria-current="page"] span {
            background-color: var(--color-primary) !important;
            color: white !important;
            border-color: var(--color-primary) !important;
        }
        .pagination-container a:focus, .pagination-container span:focus {
            box-shadow: 0 0 0 3px rgba(10, 77, 42, 0.2) !important;
        }
    </style>
    <!-- Add Tailwind via CDN for utility classes inside content if desired, though we have custom CSS above -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0a4d2a',
                        secondary: '#073b1d',
                    }
                }
            }
        }
    </script>
</head>
<body>

    <!-- Mobile Backdrop -->
    <div class="mobile-backdrop" id="mobileBackdrop"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="brand-logo">
                <div class="brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="brand-text">DCC Library</span>
            </a>
        </div>

        <ul class="nav-list">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="link-text">Dashboard</span>
                </a>
            </li>
            
            <!-- Student Data -->
            <li class="nav-item">
                <a href="{{ route('admin.student-data') }}" class="nav-link {{ request()->routeIs('admin.student-data') ? 'active' : '' }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="link-text">Student Data</span>
                </a>
            </li>

            <!-- Student Logs -->
            <li class="nav-item">
                <a href="{{ route('admin.student-logs') }}" class="nav-link {{ request()->routeIs('admin.student-logs') ? 'active' : '' }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="link-text">Student Logs</span>
                </a>
            </li>

            <!-- Employee Data -->
            <li class="nav-item">
                <a href="{{ route('admin.employee-data') }}" class="nav-link {{ request()->routeIs('admin.employee-data') ? 'active' : '' }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="link-text">Employee Data</span>
                </a>
            </li>

            <!-- Employee Logs -->
            <li class="nav-item">
                <a href="{{ route('admin.employee-logs') }}" class="nav-link {{ request()->routeIs('admin.employee-logs') ? 'active' : '' }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="link-text">Employee Logs</span>
                </a>
            </li>

            <!-- Reports -->
            <li class="nav-item">
                <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                    </svg>
                    <span class="link-text">Reports</span>
                </a>
            </li>

            <!-- Users -->
            <li class="nav-item">
                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="link-text">Users</span>
                </a>
            </li>
        </ul>
        
        <!-- Bottom / Logout -->
        <div style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);">
            <button type="button" onclick="showLogoutModal()" class="nav-link" style="width: 100%; background: none; border: none; cursor: pointer; border-left: 4px solid transparent;">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="link-text">Logout</span>
            </button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <div class="header-left">
                <button class="toggle-btn" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="page-title">@yield('header', 'Dashboard')</h1>
            </div>
            <div class="header-right">
                <!-- User Profile or other actions -->
                <span class="text-gray-600">Welcome, {{ auth()->user()->name ?? 'Admin' }}</span>
            </div>
        </header>

        <main class="content-body">
            @yield('content')
        </main>

        <footer style="padding: 1.5rem; text-align: center; color: var(--color-text-muted); font-size: 0.875rem; border-top: 1px solid var(--color-border); background: #fff;">
            Design With <span style="color: #e11d48;">❤️</span> By MIS Team
        </footer>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="hideLogoutModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 14c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Confirm Logout</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Are you sure you want to log out?</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="confirmLogout()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Logout
                    </button>
                    <button type="button" onclick="hideLogoutModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function confirmLogout() {
            document.getElementById('logout-form').submit();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const toggleBtn = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('mobileBackdrop');
            const sidebar = document.getElementById('sidebar');

            // --- State Management ---
            const STORAGE_KEY = 'sidebar:collapsed';
            const isDesktop = () => window.innerWidth >= 1024;

            // Initialize State
            function initSidebar() {
                if (isDesktop()) {
                    const collapsed = localStorage.getItem(STORAGE_KEY) === 'true';
                    if (collapsed) {
                        body.classList.add('sidebar-collapsed');
                    } else {
                        body.classList.remove('sidebar-collapsed');
                    }
                } else {
                    // Mobile: always closed initially
                    body.classList.remove('sidebar-open');
                }
            }

            initSidebar();

            // Prevent transition checks on load (optional optimization)
            setTimeout(() => {
                sidebar.style.transition = 'width 0.3s ease, margin 0.3s ease, transform 0.3s ease';
            }, 100);

            // --- Event Listeners ---
            toggleBtn.addEventListener('click', () => {
                if (isDesktop()) {
                    // Toggle Collapsed Mode
                    body.classList.toggle('sidebar-collapsed');
                    const isCollapsed = body.classList.contains('sidebar-collapsed');
                    localStorage.setItem(STORAGE_KEY, isCollapsed);
                } else {
                    // Toggle Mobile Overlay
                    body.classList.toggle('sidebar-open');
                    // Prevent body scroll when open
                    if(body.classList.contains('sidebar-open')) {
                        body.style.overflow = 'hidden';
                    } else {
                        body.style.overflow = '';
                    }
                }
            });

            // Close mobile sidebar when clicking backdrop
            backdrop.addEventListener('click', () => {
                body.classList.remove('sidebar-open');
                body.style.overflow = '';
            });

            // Handle Resize
            window.addEventListener('resize', () => {
                // If moving from mobile to desktop, clean up mobile classes
                if (window.innerWidth >= 1024) {
                    body.classList.remove('sidebar-open');
                    body.style.overflow = '';
                    // Restore desktop state
                    const collapsed = localStorage.getItem(STORAGE_KEY) === 'true';
                    if (collapsed) body.classList.add('sidebar-collapsed');
                    else body.classList.remove('sidebar-collapsed');
                } else {
                     // Moving to mobile: remove collapse class (it's weird on mobile)
                     body.classList.remove('sidebar-collapsed');
                }
            });
        });
    </script>
</body>
</html>
