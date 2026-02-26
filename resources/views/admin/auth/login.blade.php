
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Odovin — Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        :root{--accent:#a855f7;--accent-alt:#7c3aed;}
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Chakra Petch',sans-serif;background:#0a0e1a;color:#fff;min-height:100vh;
            display:flex;align-items:center;justify-content:center;}
        body::before{content:'';position:fixed;inset:0;
            background:radial-gradient(ellipse 60% 50% at 20% 20%,rgba(168,85,247,.1),transparent),
                        radial-gradient(ellipse 50% 40% at 80% 80%,rgba(124,58,237,.08),transparent);
            pointer-events:none;}
        .login-box{width:100%;max-width:420px;padding:1.5rem;}
        .logo{text-align:center;margin-bottom:2.5rem;}
        .logo-badge{display:inline-flex;align-items:center;gap:.375rem;background:linear-gradient(135deg,var(--accent),var(--accent-alt));
            padding:.35rem .875rem;border-radius:6px;font-size:.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;margin-bottom:.875rem;}
        .logo-text{font-family:'Orbitron',sans-serif;font-size:2.25rem;font-weight:900;
            background:linear-gradient(135deg,#c084fc,#a855f7,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .logo-sub{font-size:.78rem;color:rgba(255,255,255,.4);margin-top:.375rem;}
        .card{background:rgba(26,32,48,.9);backdrop-filter:blur(20px);
            border:1px solid rgba(168,85,247,.2);border-radius:20px;padding:2.25rem;}
        .card-title{font-family:'Orbitron',sans-serif;font-size:1.125rem;font-weight:700;
            margin-bottom:.375rem;text-align:center;}
        .card-sub{font-size:.8rem;color:rgba(255,255,255,.45);text-align:center;margin-bottom:2rem;}
        .form-group{margin-bottom:1.25rem;}
        label{display:block;font-size:.78rem;font-weight:600;color:rgba(255,255,255,.65);margin-bottom:.5rem;}
        input{width:100%;padding:.875rem 1rem;background:rgba(168,85,247,.07);border:1px solid rgba(168,85,247,.2);
            border-radius:10px;color:#fff;font-family:'Chakra Petch',sans-serif;font-size:.9rem;outline:none;transition:all .3s;}
        input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(168,85,247,.12);}
        input::placeholder{color:rgba(255,255,255,.25);}
        .error{color:#ff3366;font-size:.75rem;margin-top:.375rem;}
        .btn{width:100%;padding:.9rem;background:linear-gradient(135deg,var(--accent),var(--accent-alt));
            border:none;border-radius:10px;color:#fff;font-family:'Orbitron',sans-serif;font-size:.85rem;
            font-weight:700;letter-spacing:.06em;cursor:pointer;transition:all .3s;margin-top:.5rem;}
        .btn:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(168,85,247,.4);}
    </style>
</head>
<body>
<div class="login-box">
    <div class="logo">
        <div class="logo-badge">
            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Secure Admin Access
        </div>
        <div class="logo-text">ODOVIN</div>
        <div class="logo-sub">Administration Portal</div>
    </div>
    <div class="card">
        <div class="card-title">Sign In</div>
        <div class="card-sub">Enter your admin credentials to continue</div>
        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@odovin.com" required autofocus>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn">ACCESS CONTROL CENTER</button>
        </form>
    </div>
</div>
</body>
</html>