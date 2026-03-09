@extends('provider.layouts.app')
@section('title', $job->service_type . ' — Job #' . $job->job_number)
@section('content')
<style>
.pg-container { padding:2rem; max-width:900px; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; }
.back-link:hover { gap:.875rem; }
.hero-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:20px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.hero-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent-warning),var(--accent-cyan)); }
.job-title { font-family:'Orbitron',sans-serif; font-size:1.75rem; font-weight:800; margin-bottom:.5rem; }
.kv-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem; margin-top:1.25rem; }
.kv-box { background:rgba(0,212,255,.04); border:1px solid rgba(0,212,255,.1); border-radius:10px; padding:.875rem 1rem; }
.kv-box .k { font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:var(--text-tertiary); margin-bottom:.35rem; }
.kv-box .v { font-size:.95rem; font-weight:700; color:var(--text-primary); }
.desc-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; }
.section-title { font-family:'Orbitron',sans-serif; font-size:.9rem; font-weight:700; color:var(--accent-cyan); text-transform:uppercase; letter-spacing:.08em; margin-bottom:1rem; }
.desc-text { font-size:.9rem; color:var(--text-secondary); line-height:1.8; }
.offer-form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.offer-form-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.form-group { margin-bottom:0; }
.form-group.full { grid-column:1/-1; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:.5rem; }
.form-label .req { color:var(--accent-warning); }
.form-input, .form-select, .form-textarea { width:100%; padding:.75rem 1rem; background:rgba(0,212,255,.05); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; box-sizing:border-box; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:#121827; }
.form-textarea { resize:vertical; min-height:90px; }
.price-input-wrap { position:relative; }
.price-input-wrap .currency { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--accent-cyan); font-weight:700; }
.price-input-wrap .form-input { padding-left:2rem; }
.btn-submit { width:100%; padding:1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.9rem; letter-spacing:.05em; cursor:pointer; transition:all .3s; box-shadow:0 4px 15px rgba(0,212,255,.3); margin-top:1.5rem; }
.btn-submit:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.5); }
.offer-submitted-card { background:rgba(0,255,170,.06); border:2px solid rgba(0,255,170,.3); border-radius:16px; padding:2rem; text-align:center; }
.offer-submitted-card .icon { font-size:3rem; margin-bottom:1rem; }
.offer-submitted-card h3 { font-family:'Orbitron',sans-serif; font-size:1.25rem; color:var(--accent-green); margin-bottom:.75rem; }
.offer-submitted-card .details { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-top:1.5rem; text-align:left; }
.offer-detail { background:rgba(0,255,170,.05); border:1px solid rgba(0,255,170,.15); border-radius:10px; padding:.875rem; }
.offer-detail .k { font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:var(--accent-green); opacity:.7; margin-bottom:.35rem; }
.offer-detail .v { font-weight:700; }
.budget-hint { background:rgba(255,170,0,.08); border:1px solid rgba(255,170,0,.2); border-radius:10px; padding:.875rem 1rem; font-size:.8rem; color:var(--accent-warning); display:flex; gap:.75rem; align-items:center; margin-bottom:1.5rem; }
.error-msg { color:#ff8099; font-size:.775rem; margin-top:.375rem; }
</style>

<div class="pg-container">
    <a href="{{ route('provider.jobs.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Job Board
    </a>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">
        {{ session('error') }}
    </div>
    @endif

    {{-- Job hero --}}
    <div class="hero-card">
        <div class="job-title">{{ $job->service_type }}</div>
        <div style="font-size:.8rem;color:var(--text-tertiary);">Job #{{ $job->job_number }} · Posted {{ $job->created_at->diffForHumans() }}</div>

        <div class="kv-grid">
            <div class="kv-box">
                <div class="k">Vehicle</div>
                <div class="v">{{ $job->vehicle->year }} {{ $job->vehicle->make }}</div>
                <div style="font-size:.8rem;color:var(--text-tertiary);">{{ $job->vehicle->model }}</div>
            </div>
            <div class="kv-box">
                <div class="k">Customer Budget</div>
                <div class="v" style="color:var(--accent-warning);">{{ $job->budgetLabel() }}</div>
            </div>
            @if($job->preferred_date)
            <div class="kv-box">
                <div class="k">Preferred Date</div>
                <div class="v">{{ \Carbon\Carbon::parse($job->preferred_date)->format('M d, Y') }}</div>
                @if($job->preferred_time)<div style="font-size:.8rem;color:var(--text-tertiary);">{{ $job->preferred_time }}</div>@endif
            </div>
            @endif
            <div class="kv-box">
                <div class="k">Location</div>
                <div class="v" style="font-size:.8rem;line-height:1.4;">{{ \Str::limit($job->location_address, 40) ?? 'Not specified' }}</div>
            </div>
            <div class="kv-box">
                <div class="k">Offers So Far</div>
                <div class="v" style="color:var(--accent-warning);">{{ $job->offers->count() }}</div>
            </div>
            @if($job->expires_at)
            <div class="kv-box">
                <div class="k">Expires</div>
                <div class="v" style="color:{{ $job->expires_at->diffInHours() < 3 ? '#ff8099' : 'var(--accent-warning)' }};">{{ $job->expires_at->diffForHumans() }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Description --}}
    <div class="desc-card">
        <div class="section-title">Job Description</div>
        <p class="desc-text">{{ $job->description }}</p>
        @if($job->customer_notes)
        <p class="desc-text" style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-color);font-style:italic;opacity:.8;">Notes: {{ $job->customer_notes }}</p>
        @endif
    </div>


    {{-- ── Media Gallery ──────────────────────────────────────────────────── --}}
    @if($job->media && count($job->media) > 0)
    <div class="desc-card">
        <div class="section-title">📸 Photos & Videos from Customer</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:.75rem;">
            @foreach($job->media as $item)
            @if($item['type'] === 'video')
            <div style="position:relative;border-radius:10px;overflow:hidden;aspect-ratio:1;background:#000;cursor:pointer;" onclick="openLightbox('{{ $item['url'] }}','video')">
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;">
                    <div style="font-size:2.5rem;">▶️</div>
                    <div style="font-size:.7rem;margin-top:.35rem;opacity:.7;">{{ $item['name'] }}</div>
                </div>
            </div>
            @else
            <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;cursor:pointer;" onclick="openLightbox('{{ $item['url'] }}','image')">
                <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" style="width:100%;height:100%;object-fit:cover;transition:.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform=''">
            </div>
            @endif
            @endforeach
        </div>
    </div>

    {{-- Lightbox --}}
    <div id="lightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeLightbox()">
        <button onclick="closeLightbox()" style="position:absolute;top:1.5rem;right:1.5rem;background:rgba(255,255,255,.1);border:none;border-radius:50%;width:42px;height:42px;color:#fff;font-size:1.25rem;cursor:pointer;">✕</button>
        <img id="lbImg" src="" style="display:none;max-width:92vw;max-height:88vh;border-radius:10px;object-fit:contain;">
        <video id="lbVid" src="" controls style="display:none;max-width:92vw;max-height:88vh;border-radius:10px;"></video>
    </div>
    <script>
    function openLightbox(url, type) {
        const lb = document.getElementById('lightbox');
        document.getElementById('lightbox').style.display = 'flex';
        if (type === 'video') {
            document.getElementById('lbVid').src = url;
            document.getElementById('lbVid').style.display = 'block';
            document.getElementById('lbImg').style.display = 'none';
            document.getElementById('lbVid').play();
        } else {
            document.getElementById('lbImg').src = url;
            document.getElementById('lbImg').style.display = 'block';
            document.getElementById('lbVid').style.display = 'none';
        }
    }
    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.getElementById('lbVid').pause();
    }
    document.addEventListener('keydown', e => { if(e.key==='Escape') closeLightbox(); });
    </script>
    @endif

    {{-- Offer form or already-submitted banner --}}
    @if($myOffer)
    <div class="offer-submitted-card">
        <div class="icon">📋</div>
        <h3>Offer Submitted</h3>
        <p style="color:var(--text-secondary);font-size:.875rem;">Your offer is waiting for the customer's decision. You'll be notified when they respond.</p>
        <div class="details">
            <div class="offer-detail">
                <div class="k">Your Price</div>
                <div class="v" style="color:var(--accent-warning);font-family:'Orbitron',sans-serif;">${{ number_format($myOffer->offered_price, 2) }}</div>
            </div>
            <div class="offer-detail">
                <div class="k">Status</div>
                <div class="v" style="color:{{ $myOffer->status === 'accepted' ? 'var(--accent-green)' : ($myOffer->status === 'rejected' ? '#ff8099' : 'var(--accent-warning)') }};">
                    {{ ucfirst($myOffer->status) }}
                </div>
            </div>
            <div class="offer-detail">
                <div class="k">Available Date</div>
                <div class="v">{{ \Carbon\Carbon::parse($myOffer->available_date)->format('M d, Y') }}</div>
            </div>
            <div class="offer-detail">
                <div class="k">Available Time</div>
                <div class="v">{{ $myOffer->available_time }}</div>
            </div>
        </div>
        @if($myOffer->message)
        <div style="margin-top:1.25rem;background:rgba(0,255,170,.04);border:1px solid rgba(0,255,170,.15);border-radius:10px;padding:1rem;font-size:.85rem;color:var(--text-secondary);text-align:left;">
            <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:var(--accent-green);opacity:.7;margin-bottom:.5rem;">Your Message</div>
            {{ $myOffer->message }}
        </div>
        @endif
    </div>

    @elseif($job->isOpen())
    {{-- Budget hint --}}
    @if($job->budget_max)
    <div class="budget-hint">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Customer's budget: <strong>{{ $job->budgetLabel() }}</strong>. Try to stay competitive to increase your chance of being selected.
    </div>
    @endif

    <div class="offer-form-card">
        <div class="section-title">Submit Your Offer</div>
        <form method="POST" action="{{ route('provider.jobs.submit-offer', $job) }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Your Price <span class="req">*</span></label>
                    <div class="price-input-wrap">
                        <span class="currency">$</span>
                        <input type="number" name="offered_price" class="form-input" placeholder="0.00" min="1" step="0.01" required value="{{ old('offered_price') }}">
                    </div>
                    @error('offered_price')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Estimated Duration (minutes)</label>
                    <input type="number" name="estimated_duration" class="form-input" placeholder="e.g. 60" min="15" max="480" value="{{ old('estimated_duration') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Available Date <span class="req">*</span></label>
                    <input type="date" name="available_date" class="form-input" required
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        value="{{ old('available_date', $job->preferred_date ?? now()->addDay()->format('Y-m-d')) }}">
                    @error('available_date')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Available Time <span class="req">*</span></label>
                    <select name="available_time" class="form-select" required>
                        <option value="">Select time...</option>
                        @for($h = 7; $h <= 18; $h++)
                        @foreach(['00','30'] as $min)
                        @php $val = sprintf('%02d:%s', $h, $min); @endphp
                        <option value="{{ $val }}" {{ old('available_time') === $val ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                        @endfor
                    </select>
                    @error('available_time')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group full">
                    <label class="form-label">Message to Customer</label>
                    <textarea name="message" class="form-textarea" placeholder="Tell the customer why they should choose you. Mention your experience, warranty, or any relevant certifications...">{{ old('message') }}</textarea>
                    @error('message')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            <button type="submit" class="btn-submit">
                🚀 Submit My Offer
            </button>
        </form>
    </div>

    @else
    <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:2rem;text-align:center;color:var(--text-tertiary);">
        This job post is no longer accepting offers.
    </div>
    @endif
</div>
@endsection