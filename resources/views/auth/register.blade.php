<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DCC Library</title>
    <link rel="icon" type="image/png" href="{{ asset('images/DCC2.png') }}">
    
    <!-- Preload critical images -->
    <link rel="preload" as="image" href="{{ asset('images/DCC2.png') }}">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(
                135deg,
                rgba(7, 59, 29, 0.85) 0%,
                rgba(10, 77, 42, 0.85) 100%
            );
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
            max-width: 450px;
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
            margin-bottom: 1.2rem;
            position: relative;
            text-align: left;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            font-size: 1rem;
            transition: border-color 0.3s;
            background-color: #fff;
        }

        .input-wrapper input:focus {
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
            background-color: #0a4d2a;
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

        .helper-links {
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        
        .helper-links a {
            color: #0a4d2a;
            text-decoration: none;
            font-weight: 600;
        }

        .helper-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div style="margin-bottom: 2rem;">
             <img src="{{ asset('images/DCC2.png') }}" alt="Logo" style="max-width: 120px; height: auto;">
             <h3 style="margin-top: 10px; color: #333;">Create Admin Account</h3>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <!-- Name -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required>
                </div>
                @error('name') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Username -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
                </div>
                @error('username') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
                </div>
                @error('email') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                @error('password') <span class="error-message">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                </div>
            </div>

            <button type="submit" class="login-btn">
                Create Account
            </button> <!-- Added button closure -->

            <div class="helper-links">
                Already have an account? <a href="{{ route('login') }}">Login here</a>
            </div>
        </form>
    </div>

    <footer class="auth-footer">
        Design With <span style="color: #fb7185;">❤️</span> By MIS Team
    </footer>

</body>
</html>
