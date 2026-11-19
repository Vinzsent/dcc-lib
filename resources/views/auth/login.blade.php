<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/png" href="{{ asset('images/DCC2.png') }}">
    
    <!-- Preload critical images -->
    <link rel="preload" as="image" href="{{ asset('images/dcccover.jpg') }}">
    <link rel="preload" as="image" href="{{ asset('images/DCC2.png') }}">
    <link rel="preload" as="image" href="{{ asset('images/LIBRARY LOGO.webp') }}">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #073b1d; /* Fallback color matching the theme */
            background-image: linear-gradient(135deg, rgba(7, 59, 29, 0.9), rgba(10, 77, 42, 0.8)), url("{{ asset('images/dcccover.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input,
        .input-wrapper select {
            width: 100%;
            padding: 12px 12px 12px 40px; /* Space for icon */
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            font-size: 1rem;
            transition: border-color 0.3s;
            background-color: #fff;
        }

        .input-wrapper input:focus,
        .input-wrapper select:focus {
            border-color: #0a4d2a;
            box-shadow: 0 0 0 2px rgba(10, 77, 42, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #888;
            pointer-events: none;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #0a4d2a; /* Matching the gradient theme */
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            margin-top: 1rem;
        }

        .login-btn:hover {
            background-color: #073b1d;
        }

        .login-btn:active {
            transform: scale(0.98);
        }

        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        }
        /* Loading Screen */
        #loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(7, 59, 29, 0.95); /* Deep Green background matching theme */
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            border-top-color: #FEBA01; /* Gold/Yellow matching button text */
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        #loading-overlay h3 {
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0 1rem 0;
            color: #888;
            font-size: 0.75rem;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }

        .divider span {
            padding: 0 0.75rem;
        }

        .scanner-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px;
            background-color: #f0fdf4;
            color: #065f46;
            border: 1.5px solid #a7f3d0;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .scanner-btn:hover {
            background-color: #0a4d2a;
            color: #FEBA01;
            border-color: #0a4d2a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 77, 42, 0.2);
        }

        .scanner-btn svg {
            width: 18px;
            height: 18px;
        }

        .fixed-scanner-link {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: rgba(254, 186, 1, 0.85);
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-decoration: none;
            border: 1px solid rgba(254, 186, 1, 0.3);
            transition: all 0.2s ease;
            z-index: 50;
        }

        .fixed-scanner-link:hover {
            color: #FEBA01;
            background: rgba(0, 0, 0, 0.45);
            border-color: rgba(254, 186, 1, 0.6);
            transform: translateY(-1px);
        }

        .fixed-scanner-link svg {
            width: 16px;
            height: 16px;
        }
    </style>
</head>
<body>

    <!-- Scanner Top Link -->
    <a href="{{ route('scanner') }}" class="fixed-scanner-link" title="Go to Library Scanner">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        Library Scanner
    </a>

<div class="login-card">
        <div style="margin-bottom: 2rem;">
             <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 10px;">
                 <img src="{{ asset('images/DCC2.png') }}" alt="DCC Logo" style="max-width: 100px; height: auto;">
                 <img src="{{ asset('images/LIBRARY LOGO.webp') }}" alt="Library Logo" style="max-width: 100px; height: auto;">
             </div>
             <h3>Library Management System</h3>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <!-- Username -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required autofocus>
                </div>
                @error('username')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Location -->
            <div class="input-group">
                <label for="location">SELECT LOCATION</label>
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <select name="location" id="location" required>
                        <option value="">Select Location</option>
                        <option value="DCC TED">DCC TED</option>
                        <option value="DCC BED">DCC BED (All)</option>
                        <option value="DCC BED SeniorHighSchool">DCC BED - Senior High School</option>
                        <option value="DCC BED Highschool">DCC BED - High School</option>
                        <option value="DCC BED Elementary">DCC BED - Elementary</option>
                        <option value="Master">Master</option>
                    </select>
                </div>
                 @error('location')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Login Button -->
            <button type="submit" class="login-btn" style="color: #FEBA01;">
                Login
            </button>
        </form>

        <div class="divider">
            <span>OR</span>
        </div>

        <!-- Scanner Page Link -->
        <a href="{{ route('scanner') }}" class="scanner-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Go to Library Scanner
        </a>
            
    </div>

   <footer class="auth-footer mb-4" style="color: #FEBA01;">
        Design With <span>❤️</span> By <span>MIS Team</span>
    </footer>

    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="spinner"></div>
        <h3>Logging in, please wait...</h3>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            // Show the loading overlay
            document.getElementById('loading-overlay').style.display = 'flex';
        });
    </script>
</body>
</html>
