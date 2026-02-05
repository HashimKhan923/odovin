<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - VehicleTracker</title>
    
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
            --accent-warning: #ffaa00;
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
            --accent-warning: #ff9500;
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
            max-width: 520px;
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

        /* Password Grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-grid .form-group {
            margin-bottom: 0;
        }

        /* Password Strength */
        .password-strength {
            margin-top: 1rem;
            display: none;
        }

        .strength-bar {
            height: 4px;
            background: var(--border-color);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-text {
            font-size: 0.75rem;
            font-weight: 600;
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

        /* Footer */
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        .auth-footer-text {
            color: var(--text-tertiary);
            font-size: 0.75rem;
            line-height: 1.5;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .auth-card {
                padding: 2rem 1.5rem;
            }

            .auth-logo {
                font-size: 2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
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
                <h1 class="auth-logo">VehicleTracker</h1>
                <p class="auth-subtitle">Create your account to get started</p>
            </div>

            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input 
                        id="name" 
                        class="form-input" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus 
                        autocomplete="name"
                        placeholder="Enter your full name"
                    />
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

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
                        autocomplete="username"
                        placeholder="Enter your email"
                    />
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Fields -->
                <div class="form-grid">
                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            id="password" 
                            class="form-input"
                            type="password"
                            name="password"
                            required 
                            autocomplete="new-password"
                            placeholder="Create password"
                        />
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input 
                            id="password_confirmation" 
                            class="form-input"
                            type="password"
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            placeholder="Confirm password"
                        />
                        @error('password_confirmation')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Password Strength -->
                <div class="password-strength" id="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strength-fill"></div>
                    </div>
                    <div class="strength-text" id="strength-text"></div>
                </div>

                <!-- Actions -->
                <div class="auth-actions">
                    <a class="link-text" href="{{ route('login') }}">
                        Already registered?
                    </a>

                    <button type="submit" class="btn-primary">
                        Create Account
                    </button>
                </div>

                <!-- Terms -->
                <div class="auth-footer">
                    <span class="auth-footer-text">
                        By creating an account, you agree to our<br>
                        Terms of Service and Privacy Policy
                    </span>
                </div>
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

        // Password Strength Indicator
        const passwordInput = document.getElementById('password');
        const strengthContainer = document.getElementById('password-strength');
        const strengthFill = document.getElementById('strength-fill');
        const strengthText = document.getElementById('strength-text');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                strengthContainer.style.display = 'none';
                return;
            }

            strengthContainer.style.display = 'block';

            let strength = 0;
            let tips = [];

            // Length
            if (password.length >= 8) strength += 25;
            else tips.push('8+ characters');

            // Uppercase
            if (/[A-Z]/.test(password)) strength += 25;
            else tips.push('uppercase');

            // Lowercase
            if (/[a-z]/.test(password)) strength += 25;
            else tips.push('lowercase');

            // Number or special
            if (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password)) strength += 25;
            else tips.push('number/symbol');

            // Update UI
            strengthFill.style.width = strength + '%';
            
            const theme = document.documentElement.getAttribute('data-theme') || 'dark';
            
            if (strength <= 25) {
                strengthFill.style.background = '#ff3366';
                strengthText.style.color = '#ff3366';
                strengthText.textContent = 'Weak - Add: ' + tips.join(', ');
            } else if (strength <= 50) {
                strengthFill.style.background = theme === 'dark' ? '#ffaa00' : '#ff9500';
                strengthText.style.color = theme === 'dark' ? '#ffaa00' : '#ff9500';
                strengthText.textContent = 'Fair - Add: ' + tips.join(', ');
            } else if (strength <= 75) {
                strengthFill.style.background = theme === 'dark' ? '#00d4ff' : '#0066ff';
                strengthText.style.color = theme === 'dark' ? '#00d4ff' : '#0066ff';
                strengthText.textContent = 'Good - Almost there!';
            } else {
                strengthFill.style.background = theme === 'dark' ? '#00ffaa' : '#00cc88';
                strengthText.style.color = theme === 'dark' ? '#00ffaa' : '#00cc88';
                strengthText.textContent = 'Strong - Great password!';
            }
        });

        // Password Match Indicator
        const confirmInput = document.getElementById('password_confirmation');
        
        confirmInput.addEventListener('input', function() {
            if (this.value && passwordInput.value) {
                if (this.value === passwordInput.value) {
                    this.style.borderColor = getComputedStyle(document.documentElement)
                        .getPropertyValue('--accent-green').trim();
                } else {
                    this.style.borderColor = getComputedStyle(document.documentElement)
                        .getPropertyValue('--accent-danger').trim();
                }
            } else {
                this.style.borderColor = '';
            }
        });
    </script>
</body>
</html>