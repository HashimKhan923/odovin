<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Report Expired — Odovin</title>
<style>
body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#0d1117;color:#e6edf3;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0; }
.box { text-align:center;max-width:400px;padding:3rem 2rem; }
.icon { font-size:4rem;margin-bottom:1rem; }
h1 { font-size:1.5rem;font-weight:800;color:#fff;margin-bottom:.75rem; }
p { color:#8b949e;font-size:.9rem;line-height:1.7;margin-bottom:1.5rem; }
a { display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;background:linear-gradient(135deg,#00d4ff,#00ffaa);border-radius:10px;color:#000;font-weight:700;font-size:.875rem;text-decoration:none; }
</style>
</head>
<body>
<div class="box">
    <div class="icon">⏰</div>
    <h1>This Report Has Expired</h1>
    <p>The owner set an expiry date on this report link and it is no longer accessible. Contact the vehicle owner to request a new link.</p>
    <a href="{{ url('/') }}">Go to Odovin →</a>
</div>
</body>
</html>