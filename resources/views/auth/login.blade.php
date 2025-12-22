<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login - STIH Adhyaksa</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo_stih_white.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_stih_white.png') }}">

    <!-- Custom fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#b2202c',
                            hover: '#9a1b25',
                            dark: '#821620'
                        }
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        /* Subtle pattern background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(178, 32, 44, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(178, 32, 44, 0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            position: relative;
            z-index: 1;
        }

        .input-group {
            position: relative;
        }

        .input-group input {
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            box-shadow: 0 0 0 3px rgba(178, 32, 44, 0.1);
            border-color: #b2202c;
        }

        .input-group input:focus + .input-icon {
            color: #b2202c;
        }

        .input-icon {
            transition: all 0.2s ease;
        }

        /* Button hover effect */
        .btn-login {
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(178, 32, 44, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Logo animation */
        .logo-container {
            transition: transform 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
        }

        .logo-container:hover .logo-box {
            box-shadow: 0 8px 20px rgba(178, 32, 44, 0.4);
        }

        .logo-box {
            transition: all 0.3s ease;
        }

        /* Logo shine animation */
        @keyframes shine {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(30deg);
            }
            100% {
                transform: translateX(100%) translateY(100%) rotate(30deg);
            }
        }

        .logo-container:hover .logo-shine {
            animation: shine 0.6s ease-in-out;
        }

        .logo-shine {
            pointer-events: none;
        }

        /* Custom checkbox */
        input[type="checkbox"] {
            accent-color: #b2202c;
            cursor: pointer;
        }

        input[type="checkbox"]:checked {
            background-color: #b2202c;
            border-color: #b2202c;
        }

        input[type="checkbox"]:focus {
            ring-color: #b2202c;
            box-shadow: 0 0 0 3px rgba(178, 32, 44, 0.2);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #b2202c;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #821620;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md">
        <div class="login-card rounded-2xl shadow-xl p-10">
            
            <!-- Logo Section -->
            <div class="text-center mb-10">
                <div class="flex justify-center mb-5">
                    <div class="logo-container relative cursor-pointer">
                        <!-- Outer Glow -->
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-primary-dark/20 rounded-2xl blur-xl"></div>
                        <!-- Middle Ring -->
                        <div class="relative bg-gradient-to-br from-gray-100 to-gray-50 rounded-2xl p-1 shadow-lg">
                            <!-- Inner Logo Box -->
                            <div class="logo-box w-20 h-20 bg-gradient-to-br from-primary via-primary to-primary-dark rounded-xl flex items-center justify-center shadow-inner relative overflow-hidden">
                                <!-- Shine Effect -->
                                <div class="logo-shine absolute inset-0 w-full h-full bg-gradient-to-br from-transparent via-white/30 to-transparent"></div>
                                <img src="{{ asset('images/logo_stih_white.png') }}" alt="STIH Logo" class="w-12 h-12 relative z-10 drop-shadow-lg">
                            </div>
                        </div>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                <p class="text-gray-500 text-sm">Please sign in to your account</p>
            </div>

            <!-- Error Messages -->
            @if (session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                        <span class="text-red-700 text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                        <span class="text-green-700 text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.post') }}" method="POST" autocomplete="on">
                @csrf
                
                <!-- Email Input -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">
                        Email Address
                    </label>
                    <div class="input-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3.5 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:border-primary bg-gray-50 @error('email') border-red-500 bg-red-50 @enderror" 
                            placeholder="your.email@example.com"
                            autocomplete="email"
                            required
                            autofocus>
                        <i class="input-icon fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                        Password
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-3.5 pl-12 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-primary bg-gray-50 @error('password') border-red-500 bg-red-50 @enderror" 
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required>
                        <i class="input-icon fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <button 
                            type="button" 
                            id="togglePassword"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary transition-colors duration-200 focus:outline-none"
                            title="Show/Hide Password">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-7">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="btn-login w-full bg-gradient-to-r from-primary to-primary-dark text-white font-semibold py-3.5 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-primary focus:ring-opacity-30">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
            </form>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1"></i>
                    © 2025 STIH Adhyaksa. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });

        // Input focus effects
        const inputs = document.querySelectorAll('input[type="email"], input[type="password"], input[type="text"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.backgroundColor = '#ffffff';
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.style.backgroundColor = '#f9fafb';
                }
            });
        });
    </script>

</body>

</html>
