@extends('layouts.app')
@section('title', $provider->name)
@section('content')
<style>
:root[data-theme="dark"] {
    --card-bg:rgba(26,32,48,.85); --border-color:rgba(0,212,255,.1);
    --input-bg:rgba(0,212,255,.05); --text-primary:#fff;
    --text-secondary:rgba(255,255,255,.7); --text-tertiary:rgba(255,255,255,.45);
    --accent-cyan:#00d4ff; --accent-green:#00ffaa; --accent-warning:#ffaa00;
}
:root[data-theme="light"] {
    --card-bg:rgba(255,255,255,.9); --border-color:rgba(0,0,0,.1);
    --input-bg:rgba(0,0,0,.03); --text-primary:#1a1f36;
    --text-secondary:rgba(26,31,54,.7); --text-tertiary:rgba(26,31,54,.45);
    --accent-cyan:#0066ff; --accent-green:#00cc88; --accent-warning:#ff9500;
}
.sp-container { max-width:1200px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:all .3s; }
.back-link:hover { gap:.875rem; text-shadow:0 0 10px var(--accent-cyan); }
.hero-card { background:var(--card-bg); backdrop-filter:blur(20px); border:1px solid var(--border-color); border-radius:20px; padding:2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; }
.hero-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.hero-top { display:flex; justify-content:space-between; align-items:flex-start; gap:1.5rem; flex-wrap:wrap; }
.provider-name { font-family:'Orbitron',sans-serif; font-size:2rem; font-weight:800; margin-bottom:.75rem; }
.badges { display:flex; gap:.625rem; flex-wrap:wrap; margin-bottom:1rem; }
.badge { padding:.3rem .875rem; border-radius:12px; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; display:inline-flex; align-items:center; gap:.375rem; }
.badge-type { background:rgba(0,212,255,.15); color:var(--accent-cyan); border:1px solid rgba(0,212,255,.3); }
.badge-verified { background:rgba(0,255,170,.15); color:var(--accent-green); border:1px solid rgba(0,255,170,.3); }
.badge svg { width:14px; height:14px; }
.rating-block { text-align:center; min-width:120px; }
.rating-big { font-family:'Orbitron',sans-serif; font-size:3rem; font-weight:900; color:var(--accent-warning); line-height:1; }
.stars-row { display:flex; justify-content:center; gap:3px; margin:.5rem 0; }
.stars-row svg { width:18px; height:18px; }
.rating-sub { font-size:.8rem; color:var(--text-tertiary); }
.contact-strip { display:flex; gap:1.5rem; flex-wrap:wrap; margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid var(--border-color); }
.contact-item { display:flex; align-items:center; gap:.625rem; font-size:.875rem; color:var(--text-secondary); }
.contact-item a { color:var(--accent-cyan); text-decoration:none; }
.contact-item a:hover { text-decoration:underline; }
.contact-item svg { width:18px; height:18px; color:var(--accent-cyan); flex-shrink:0; }
.main-grid { display:grid; grid-template-columns:1fr 380px; gap:1.5rem; }
.card { background:var(--card-bg); backdrop-filter:blur(20px); border:1px solid var(--border-color); border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; }
.card-title { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border-color); }
.services-tags { display:flex; flex-wrap:wrap; gap:.5rem; }
.svc-tag { padding:.375rem .875rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); border-radius:20px; font-size:.8rem; color:var(--text-secondary); }
.review-item { padding:.875rem 0; border-bottom:1px solid rgba(0,212,255,.06); }
.review-item:last-child { border-bottom:none; }
.rev-head { display:flex; align-items:center; gap:.75rem; margin-bottom:.5rem; }
.rev-stars { display:flex; gap:2px; }
.rev-stars svg { width:14px; height:14px; }
.rev-date { font-size:.75rem; color:var(--text-tertiary); }
.rev-text { font-size:.875rem; color:var(--text-secondary); line-height:1.6; }
.rev-svc { font-size:.75rem; color:var(--text-tertiary); margin-top:.375rem; }
.rb-row { display:flex; align-items:center; gap:.75rem; margin-bottom:.5rem; }
.rb-lbl { width:20px; font-size:.8rem; font-weight:700; color:var(--accent-warning); text-align:right; flex-shrink:0; }
.rb-track { flex:1; height:8px; background:rgba(255,255,255,.07); border-radius:4px; overflow:hidden; }
.rb-fill { height:100%; background:linear-gradient(90deg,var(--accent-warning),rgba(255,170,0,.5)); border-radius:4px; }
.rb-cnt { width:28px; font-size:.75rem; color:var(--text-tertiary); text-align:right; flex-shrink:0; }
.booking-sticky { position:sticky; top:5rem; }
.form-group { margin-bottom:1.25rem; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:.5rem; }
.form-select { width:100%; padding:.75rem 1rem; background:var(--input-bg); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:all .3s; }
.form-select:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.1); }
.form-select option { background:rgba(18,24,39,1); }
.btn-book { width:100%; padding:1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.875rem; letter-spacing:.05em; cursor:pointer; transition:all .3s; box-shadow:0 4px 15px rgba(0,212,255,.3); }
.btn-book:hover { transform:translateY(-2px); box-shadow:0 6px 25px rgba(0,212,255,.5); }
.phone-cta { display:block; text-align:center; margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid var(--border-color); }
.phone-num { font-family:'Orbitron',sans-serif; font-size:1.25rem; font-weight:700; color:var(--accent-cyan); text-decoration:none; }
.phone-num:hover { text-shadow:0 0 10px var(--accent-cyan); }
.booked-dates { display:flex; flex-wrap:wrap; gap:.375rem; margin-top:.625rem; }
.booked-date { padding:.25rem .625rem; background:rgba(255,51,102,.1); border:1px solid rgba(255,51,102,.25); border-radius:6px; font-size:.7rem; color:#ff8099; }
@media(max-width:960px) { .main-grid { grid-template-columns:1fr; } .booking-sticky { position:relative; top:0; } }
@media(max-width:600px) { .hero-top { flex-direction:column; } }
</style>

<div class="sp-container">
    <a href="{{ route('providers.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Providers
    </a>

    <div class="hero-card">
        <div class="hero-top">
            <div style="flex:1;">
                <div class="provider-name">{{ $provider->name }}</div>
                <div class="badges">
                    <span class="badge badge-type">{{ ucfirst(str_replace('_',' ',$provider->type)) }}</span>
                    @if($provider->is_verified)
                    <span class="badge badge-verified">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                        Verified
                    </span>
                    @endif
                </div>
            </div>
            @if($provider->rating > 0)
            <div class="rating-block">
                <div class="rating-big">{{ number_format($provider->rating,1) }}</div>
                <div class="stars-row">
                    @for($i=1;$i<=5;$i++)
                    <svg fill="{{ $i<=round($provider->rating) ? 'var(--accent-warning)' : 'rgba(255,255,255,.15)' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <div class="rating-sub">{{ $provider->total_reviews }} reviews</div>
            </div>
            @endif
        </div>
        <div class="contact-strip">
            @if($provider->phone)
            <div class="contact-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <a href="tel:{{ $provider->phone }}">{{ $provider->phone }}</a>
            </div>
            @endif
            @if($provider->email)
            <div class="contact-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <a href="mailto:{{ $provider->email }}">{{ $provider->email }}</a>
            </div>
            @endif
            <div class="contact-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>{{ $provider->address }}, {{ $provider->city }}, {{ $provider->state }}</span>
            </div>
        </div>
    </div>

    <div class="main-grid">
        <div>
          
            <div class="card">
                <div class="card-title">Services Offered</div>
                <div class="services-tags">
                    @foreach($provider->services_offered as $svc)
                    <span class="svc-tag">{{ trim($svc) }}</span>
                    @endforeach
                </div>
            </div>
            

            <div class="card">
                <div class="card-title">Customer Reviews</div>
                @if($totalRated > 0)
                <div style="margin-bottom:1.25rem;">
                    @foreach([5,4,3,2,1] as $star)
                    <div class="rb-row">
                        <div class="rb-lbl">{{ $star }}</div>
                        <div class="rb-track">
                            <div class="rb-fill" style="width:{{ $totalRated > 0 ? max(0, ($ratingBreakdown[$star] ?? 0)/$totalRated*100) : 0 }}%;"></div>
                        </div>
                        <div class="rb-cnt">{{ $ratingBreakdown[$star] ?? 0 }}</div>
                    </div>
                    @endforeach
                </div>
                @endif
                @forelse($provider->bookings as $booking)
                <div class="review-item">
                    <div class="rev-head">
                        <div class="rev-stars">
                            @for($i=1;$i<=5;$i++)
                            <svg fill="{{ $i<=$booking->rating ? 'var(--accent-warning)' : 'rgba(255,255,255,.15)' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="rev-date">{{ $booking->updated_at->format('M d, Y') }}</span>
                    </div>
                    @if($booking->review)<p class="rev-text">{{ $booking->review }}</p>@endif
                    <div class="rev-svc">Service: {{ $booking->service_type }}</div>
                </div>
                @empty
                <p style="color:var(--text-tertiary);font-size:.875rem;text-align:center;padding:2rem 0;">No reviews yet — be the first to book!</p>
                @endforelse
            </div>
        </div>

        <div class="booking-sticky">
            <div class="card">
                <div class="card-title">Book a Service</div>
                <form action="{{ route('bookings.create') }}" method="GET">
                    <input type="hidden" name="service_provider_id" value="{{ $provider->id }}">
                    <div class="form-group">
                        <label class="form-label">Select Your Vehicle</label>
                        <select name="vehicle_id" class="form-select" required>
                            <option value="">Choose a vehicle...</option>
                            @foreach($userVehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(!empty($bookedSlots))
                    <div class="form-group">
                        <div class="form-label">Busy Dates (next 14 days)</div>
                        <div class="booked-dates">
                            @foreach(array_unique($bookedSlots) as $slot)
                            <span class="booked-date">{{ \Carbon\Carbon::parse($slot)->format('M d') }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <button type="submit" class="btn-book">Continue to Booking →</button>
                </form>
                <div class="phone-cta">
                    <div style="font-size:.8rem;color:var(--text-tertiary);margin-bottom:.5rem;">Or call directly</div>
                    <a href="tel:{{ $provider->phone }}" class="phone-num">{{ $provider->phone }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection