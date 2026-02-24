<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Provider Login — Odovin</title>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Chakra Petch', sans-serif;
            background: #0a0e1a; color: #fff;
            min-height: 100vh; display: flex;
            align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        body::before {
            content: ''; position: fixed; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 30% 20%, rgba(0,212,255,.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(0,255,170,.06) 0%, transparent 50%);
            pointer-events: none;
        }
        .login-card {
            width: 100%; max-width: 420px; margin: 2rem;
            background: rgba(26,32,48,.9); backdrop-filter: blur(20px);
            border: 1px solid rgba(0,212,255,.15); border-radius: 24px; padding: 2.5rem;
        }
        .logo-wrap { text-align: center; margin-bottom: 2rem; }
        .logo-icon {
            width: 64px; height: 64px; border-radius: 16px;
            background: linear-gradient(135deg, #00d4ff, #00ffaa);
            display: inline-flex; align-items: center; justify-content: center;
            font-family: 'Orbitron', sans-serif; font-weight: 900;
            font-size: 1.5rem; color: #000; margin-bottom: 1rem;
        }
        .logo-title {
            font-family: 'Orbitron', sans-serif; font-size: 1.5rem; font-weight: 900;
            background: linear-gradient(135deg, #00d4ff, #00ffaa);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .logo-sub { font-size: .8rem; color: rgba(255,255,255,.5); margin-top: 4px; }
        h1 { font-family: 'Orbitron', sans-serif; font-size: 1.25rem; font-weight: 700; margin-bottom: .375rem; }
        .subtitle { color: rgba(255,255,255,.55); font-size: .875rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: .8rem; font-weight: 600; color: rgba(255,255,255,.7); margin-bottom: .5rem; }
        .form-input {
            width: 100%; padding: .875rem 1rem;
            background: rgba(0,212,255,.05); border: 1px solid rgba(0,212,255,.12);
            border-radius: 12px; color: #fff; font-family: 'Chakra Petch', sans-serif;
            font-size: .875rem; transition: all .3s;
        }
        .form-input:focus { outline: none; border-color: #00d4ff; box-shadow: 0 0 0 3px rgba(0,212,255,.1); }
        .form-input::placeholder { color: rgba(255,255,255,.3); }
        .error-msg { color: #ff3366; font-size: .75rem; margin-top: .375rem; }
        .btn-login {
            width: 100%; padding: 1rem;
            background: linear-gradient(135deg, #00d4ff, #00ffaa);
            border: none; border-radius: 12px;
            color: #000; font-family: 'Orbitron', sans-serif;
            font-weight: 800; font-size: .875rem; letter-spacing: .05em;
            cursor: pointer; transition: all .3s; margin-top: .5rem;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(0,212,255,.4); }
        .divider { border: none; border-top: 1px solid rgba(0,212,255,.12); margin: 1.5rem 0; }
        .back-link { display: block; text-align: center; font-size: .8rem; color: rgba(255,255,255,.5); text-decoration: none; transition: color .3s; }
        .back-link:hover { color: #00d4ff; }
        .alert-error {
            background: rgba(255,51,102,.12); border: 1px solid rgba(255,51,102,.3);
            color: #ff3366; padding: .875rem 1rem; border-radius: 10px;
            font-size: .875rem; margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="logo-wrap">
        <div class="logo-icon">O</div>
        <div class="logo-title">ODOVIN</div>
        <div class="logo-sub">Service Provider Portal</div>
    </div>

    <h1>Welcome Back</h1>
    <p class="subtitle">Sign in to manage your bookings & services</p>

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('provider.login.submit') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-input"
                   placeholder="you@yourshop.com" value="{{ old('email') }}" required autofocus>
            @error('email')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-login">Sign In to Portal</button>
    </form>

    <hr class="divider">
    <a href="{{ route('home') }}" class="back-link">← Back to main site</a>
</div>
</body>
</html>