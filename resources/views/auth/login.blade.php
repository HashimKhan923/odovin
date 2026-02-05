<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - odovin</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Theme Variables */
        :root[data-theme="dark"] {
            --page-bg: #0a0e1a;
            --card-bg: rgba(26, 32, 48, 0.9);
            --border-color: rgba(0, 212, 255, 0.15);
            --input-bg: rgba(0, 212, 255, 0.05);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-tertiary: rgba(255, 255, 255, 0.5);
            --accent-cyan: #00d4ff;
            --accent-green: #00ffaa;
            --accent-danger: #ff3366;
        }

        :root[data-theme="light"] {
            --page-bg: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.95);
            --border-color: rgba(0, 0, 0, 0.1);
            --input-bg: rgba(0, 0, 0, 0.02);
            --text-primary: #1a1f36;
            --text-secondary: rgba(26, 31, 54, 0.7);
            --text-tertiary: rgba(26, 31, 54, 0.5);
            --accent-cyan: #0066ff;
            --accent-green: #00cc88;
            --accent-danger: #ff3366;
        }

        body {
            font-family: 'Chakra Petch', sans-serif;
            background: var(--page-bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            transition: background 0.3s ease;
        }

        /* Background Pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(0, 212, 255, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 255, 170, 0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        /* Header */
        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .auth-logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .auth-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Theme Toggle */
        .theme-toggle {
            position: fixed;
            top: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
        }

        .theme-toggle svg {
            width: 24px;
            height: 24px;
            stroke: var(--accent-cyan);
        }

        /* Status Message */
        .status-message {
            padding: 1rem;
            background: rgba(0, 255, 170, 0.1);
            border: 1px solid rgba(0, 255, 170, 0.3);
            border-radius: 12px;
            color: var(--accent-green);
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 0.875rem;
            font-family: 'Chakra Petch', sans-serif;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-cyan);
            background: rgba(0, 212, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-tertiary);
        }

        .error-message {
            color: var(--accent-danger);
            font-size: 0.75rem;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        /* Checkbox */
        .checkbox-container {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }

        .checkbox-input {
            width: 18px;
            height: 18px;
            accent-color: var(--accent-cyan);
            cursor: pointer;
            margin-right: 0.5rem;
        }

        .checkbox-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            cursor: pointer;
            user-select: none;
        }

        /* Actions */
        .auth-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 2rem;
            gap: 1rem;
        }

        .link-text {
            color: var(--accent-cyan);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .link-text:hover {
            text-shadow: 0 0 10px var(--accent-cyan);
        }

        .btn-primary {
            padding: 0.875rem 2rem;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
            font-family: 'Chakra Petch', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 212, 255, 0.5);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Footer Link */
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        .auth-footer-text {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .auth-card {
                padding: 2rem 1.5rem;
            }

            .auth-logo {
                font-size: 2rem;
            }

            .auth-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-primary {
                width: 100%;
                text-align: center;
            }

            .theme-toggle {
                top: 1rem;
                right: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Theme Toggle -->
    <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </button>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Header -->
            <div class="auth-header">
                <h1 class="auth-logo">odovin</h1>
                <p class="auth-subtitle">Welcome back! Sign in to continue</p>
            </div>

            <!-- Status Message -->
            @if (session('status'))
                <div class="status-message">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        id="email" 
                        class="form-input" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        placeholder="Enter your email"
                    />
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        id="password" 
                        class="form-input"
                        type="password"
                        name="password"
                        required 
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="checkbox-container">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        class="checkbox-input" 
                        name="remember"
                    >
                    <label for="remember_me" class="checkbox-label">
                        Remember me for 30 days
                    </label>
                </div>

                <!-- Actions -->
                <div class="auth-actions">
                    @if (Route::has('password.request'))
                        <a class="link-text" href="{{ route('password.request') }}">
                            Forgot Password?
                        </a>
                    @endif

                    <button type="submit" class="btn-primary">
                        Sign In
                    </button>
                </div>

                <!-- Register Link -->
                @if (Route::has('register'))
                    <div class="auth-footer">
                        <span class="auth-footer-text">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="link-text">Create one now</a>
                        </span>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        // Theme Management
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Load saved theme
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</body>
</html>