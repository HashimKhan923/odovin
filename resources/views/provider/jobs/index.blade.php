@extends('provider.layouts.app')
@section('title', 'Job Board')
@section('content')
<style>
.pg-container { padding:2rem; }
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem; }
.page-title { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; }
.page-title span { color:var(--accent-cyan); }
.live-indicator { display:inline-flex; align-items:center; gap:.5rem; padding:.35rem .875rem; background:rgba(0,255,170,.08); border:1px solid rgba(0,255,170,.25); border-radius:20px; font-size:.75rem; color:var(--accent-green); font-weight:600; }
.live-dot { width:8px; height:8px; border-radius:50%; background:var(--accent-green); animation:pulse-dot 1.5s infinite; }
@keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.7)} }
.live-indicator.offline { color:#ff8099; border-color:rgba(255,51,102,.25); background:rgba(255,51,102,.06); }
.live-indicator.offline .live-dot { background:#ff8099; animation:none; }
.tab-row { display:flex; gap:.5rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.tab-btn { padding:.5rem 1.25rem; border-radius:20px; border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); font-family:'Chakra Petch',sans-serif; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:none; transition:all .3s; }
.tab-btn.active, .tab-btn:hover { border-color:var(--accent-cyan); color:var(--accent-cyan); background:rgba(0,212,255,.08); }
.filter-bar { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1rem 1.5rem; margin-bottom:1.5rem; display:flex; gap:1rem; flex-wrap:wrap; align-items:center; }
.filter-select { padding:.5rem .875rem; background:rgba(0,212,255,.05); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.85rem; }
.toast-stack { position:fixed; top:1.5rem; right:1.5rem; z-index:9999; display:flex; flex-direction:column; gap:.75rem; pointer-events:none; }
.toast { background:rgba(18,24,39,.97); border:1px solid rgba(0,255,170,.4); border-radius:14px; padding:1rem 1.25rem; min-width:280px; max-width:360px; box-shadow:0 8px 30px rgba(0,0,0,.5); display:flex; gap:.875rem; align-items:flex-start; pointer-events:all; animation:slideIn .3s ease; }
@keyframes slideIn { from{transform:translateX(120%);opacity:0} to{transform:translateX(0);opacity:1} }
.toast.fade-out { animation:fadeOut .4s ease forwards; }
@keyframes fadeOut { to{transform:translateX(120%);opacity:0} }
.toast-icon { font-size:1.5rem; flex-shrink:0; }
.toast-body { flex:1; }
.toast-title { font-weight:700; font-size:.875rem; color:#fff; margin-bottom:.25rem; }
.toast-msg { font-size:.8rem; color:rgba(255,255,255,.65); }
.toast-close { font-size:1.1rem; color:rgba(255,255,255,.3); cursor:pointer; background:none; border:none; padding:0; line-height:1; }
.toast-close:hover { color:#fff; }
.toast-action { display:inline-block; margin-top:.5rem; font-size:.78rem; color:var(--accent-cyan); text-decoration:none; font-weight:600; }
.jobs-list { display:flex; flex-direction:column; gap:1rem; }
.job-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; position:relative; overflow:hidden; transition:all .4s; }
.job-card:hover { border-color:rgba(0,212,255,.3); }
.job-card.already-offered { border-color:rgba(0,255,170,.2); background:rgba(0,255,170,.03); }
.job-card.new-arrival { animation:newCard .6s ease; }
@keyframes newCard { 0%{transform:translateY(-20px);opacity:0} 60%{transform:translateY(4px)} 100%{transform:translateY(0);opacity:1} }
.job-card.removing { animation:removeCard .4s ease forwards; }
@keyframes removeCard { to{opacity:0;max-height:0;padding:0;margin:0;border-width:0} }
.job-card::before { content:''; position:absolute; top:0; left:0; bottom:0; width:3px; }
.job-card.urgency-high::before   { background:var(--accent-danger); }
.job-card.urgency-medium::before { background:var(--accent-warning); }
.job-card.urgency-low::before    { background:var(--accent-cyan); }
.job-layout { display:grid; grid-template-columns:1fr auto; gap:1.5rem; align-items:start; }
.job-type { font-family:'Orbitron',sans-serif; font-size:1.1rem; font-weight:700; margin-bottom:.5rem; }
.job-budget { font-family:'Orbitron',sans-serif; font-size:1.5rem; font-weight:800; color:var(--accent-warning); white-space:nowrap; text-align:right; }
.meta-strip { display:flex; gap:1.25rem; flex-wrap:wrap; margin-top:.75rem; font-size:.825rem; color:var(--text-secondary); }
.meta-item { display:flex; align-items:center; gap:.4rem; }
.meta-item svg { width:14px; height:14px; color:var(--accent-cyan); }
.job-desc { font-size:.825rem; color:var(--text-secondary); line-height:1.6; margin-top:.75rem; }
.action-row { display:flex; gap:.75rem; align-items:center; margin-top:1.1rem; flex-wrap:wrap; }
.btn-offer { display:inline-flex; align-items:center; gap:.5rem; padding:.625rem 1.25rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:8px; color:#000; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.75rem; cursor:pointer; text-decoration:none; transition:all .3s; }
.btn-offer:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(0,212,255,.4); }
.btn-view { display:inline-flex; align-items:center; gap:.5rem; padding:.625rem 1.1rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.25); border-radius:8px; color:var(--accent-cyan); font-size:.8rem; font-weight:600; text-decoration:none; transition:all .3s; }
.badge-offered { display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .75rem; background:rgba(0,255,170,.1); border:1px solid rgba(0,255,170,.3); border-radius:20px; font-size:.72rem; color:var(--accent-green); font-weight:600; }
.distance-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .7rem; background:rgba(0,212,255,.08); border:1px solid rgba(0,212,255,.2); border-radius:20px; font-size:.75rem; color:var(--accent-cyan); }
.vehicle-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .7rem; background:rgba(255,170,0,.08); border:1px solid rgba(255,170,0,.2); border-radius:20px; font-size:.75rem; color:var(--accent-warning); }
.urgency-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .7rem; border-radius:20px; font-size:.7rem; font-weight:700; }
.urgency-high   { background:rgba(255,51,102,.12); color:#ff8099; border:1px solid rgba(255,51,102,.3); }
.urgency-medium { background:rgba(255,170,0,.12); color:var(--accent-warning); border:1px solid rgba(255,170,0,.3); }
.new-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .75rem; background:rgba(0,255,170,.15); border:1px solid rgba(0,255,170,.4); border-radius:20px; font-size:.7rem; font-weight:700; color:var(--accent-green); }
.offer-count-badge { padding:.25rem .625rem; background:rgba(255,170,0,.1); border:1px solid rgba(255,170,0,.2); border-radius:8px; font-size:.72rem; color:var(--accent-warning); }
.empty-state { text-align:center; padding:5rem 2rem; color:var(--text-tertiary); }
</style>

<div class="toast-stack" id="toastStack"></div>

<div class="pg-container">
    <div class="page-header">
        <div>
            <div class="page-title">Job <span>Board</span></div>
            <div style="color:var(--text-tertiary);font-size:.85rem;margin-top:.35rem;">Browse open service requests from nearby customers</div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;">
            <div class="live-indicator" id="liveIndicator">
                <span class="live-dot"></span>
                <span id="liveLabel">Connecting...</span>
            </div>
            <span id="jobCountLabel" style="font-family:'Orbitron',sans-serif;font-size:.8rem;color:var(--text-tertiary);">{{ $jobs->total() }} near you</span>
        </div>
    </div>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">{{ session('error') }}</div>
    @endif

    <div class="tab-row">
        <a href="{{ route('provider.jobs.index') }}" class="tab-btn active">🗺 Open Jobs Nearby</a>
        <a href="{{ route('provider.jobs.my-offers') }}" class="tab-btn">📋 My Submitted Offers</a>
    </div>

    <div class="filter-bar">
        <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;width:100%;">
            <select name="service_type" class="filter-select" onchange="this.form.submit()">
                <option value="">All Service Types</option>
                @foreach($serviceTypes as $t)
                <option value="{{ $t }}" {{ request('service_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
            <select name="radius" class="filter-select" onchange="this.form.submit()">
                @foreach([10,25,50,100] as $r)
                <option value="{{ $r }}" {{ request('radius', 50) == $r ? 'selected' : '' }}>Within {{ $r }} miles</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="jobs-list" id="jobList">
        @forelse($jobs as $job)
        @php
            $hoursLeft = $job->expires_at ? now()->diffInHours($job->expires_at, false) : 999;
            $urgency   = $hoursLeft < 3 ? 'high' : ($hoursLeft < 8 ? 'medium' : 'low');
            $alreadyOffered = in_array($job->id, $myOfferJobIds);
        @endphp
        <div class="job-card urgency-{{ $urgency }} {{ $alreadyOffered ? 'already-offered' : '' }}" id="job-{{ $job->id }}" data-job-id="{{ $job->id }}">
            <div class="job-layout">
                <div>
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
                        <div class="job-type">{{ $job->service_type }}</div>
                        <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
                            @if($urgency === 'high')<span class="urgency-badge urgency-high">🔥 Expires soon</span>@elseif($urgency === 'medium')<span class="urgency-badge urgency-medium">⏳ {{ $hoursLeft }}h left</span>@endif
                            @if($alreadyOffered)<span class="badge-offered">✓ Offer Submitted</span>@endif
                            @if($job->offers->count() > 0)<span class="offer-count-badge">{{ $job->offers->count() }} offer{{ $job->offers->count()!==1?'s':'' }}</span>@endif
                        </div>
                    </div>
                    <div class="meta-strip">
                        @if(isset($job->distance))<span class="distance-pill">📍 {{ round($job->distance,1) }} mi</span>@endif
                        <span class="vehicle-badge">{{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}</span>
                        @if($job->preferred_date)<span class="meta-item">📅 {{ \Carbon\Carbon::parse($job->preferred_date)->format('M d') }}{{ $job->preferred_time?' · '.$job->preferred_time:'' }}</span>@endif
                    </div>
                    <p class="job-desc">{{ \Str::limit($job->description, 150) }}</p>
                    <div class="action-row">
                        @if(!$alreadyOffered)<a href="{{ route('provider.jobs.show', $job) }}" class="btn-offer">💰 Submit Offer</a>@endif
                        <a href="{{ route('provider.jobs.show', $job) }}" class="btn-view">Details →</a>
                    </div>
                </div>
                <div style="text-align:right;min-width:90px;">
                    <div class="job-budget">{{ $job->budgetLabel() }}</div>
                    <div style="font-size:.7rem;color:var(--text-tertiary);margin-top:.4rem;">{{ $job->created_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state" id="emptyState">
            <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:.2;margin-bottom:1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3 style="font-family:'Orbitron',sans-serif;font-size:1.1rem;color:var(--text-secondary);margin-bottom:.75rem;">No Open Jobs Near You</h3>
            <p>When customers post jobs in your area, they'll appear here instantly.</p>
        </div>
        @endforelse
    </div>
    @if($jobs->hasPages())<div style="margin-top:2rem;">{{ $jobs->links() }}</div>@endif
</div>

<script>
(function () {
    const CFG = {
        pollInterval: 5000,
        lat:    {{ auth()->user()->serviceProvider?->latitude ?? 'null' }},
        lng:    {{ auth()->user()->serviceProvider?->longitude ?? 'null' }},
        radius: {{ (int)request('radius', 50) }},
        userId: {{ auth()->id() }},
        myOfferIds: new Set({{ Js::from($myOfferJobIds) }}),
        csrf: document.querySelector('meta[name="csrf-token"]').content,
    };
    let lastPoll = Math.floor(Date.now()/1000) - 15;
    let pollTimer = null;
    let useEcho = false;

    // Toast helper
    function toast({icon='🔔', title, message, url=null}) {
        const stack = document.getElementById('toastStack');
        const el = document.createElement('div');
        el.className = 'toast';
        el.innerHTML = `<div class="toast-icon">${icon}</div>
            <div class="toast-body">
                <div class="toast-title">${title}</div>
                <div class="toast-msg">${message}</div>
                ${url?`<a href="${url}" class="toast-action">View Job →</a>`:''}
            </div>
            <button class="toast-close" onclick="this.closest('.toast').remove()">×</button>`;
        stack.prepend(el);
        setTimeout(()=>{el.classList.add('fade-out');setTimeout(()=>el.remove(),400);},7000);
    }

    function setLive(on) {
        const ind = document.getElementById('liveIndicator');
        document.getElementById('liveLabel').textContent = on ? (useEcho ? 'Live · WebSocket' : 'Live · Polling') : 'Reconnecting...';
        on ? ind.classList.remove('offline') : ind.classList.add('offline');
    }

    function haversine(la1,lo1,la2,lo2) {
        if(!la1||!lo1||!la2||!lo2) return null;
        const R=3959,dl=(la2-la1)*Math.PI/180,dg=(lo2-lo1)*Math.PI/180;
        const a=Math.sin(dl/2)**2+Math.cos(la1*Math.PI/180)*Math.cos(la2*Math.PI/180)*Math.sin(dg/2)**2;
        return Math.round(3959*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a))*10)/10;
    }

    function buildCard(job) {
        const dist = job.distance ?? haversine(CFG.lat,CFG.lng,job.latitude,job.longitude);
        const offered = CFG.myOfferIds.has(job.id);
        return `<div class="job-card urgency-low new-arrival ${offered?'already-offered':''}" id="job-${job.id}" data-job-id="${job.id}">
            <div class="job-layout">
                <div>
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
                        <div class="job-type">${job.service_type}</div>
                        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                            <span class="new-badge">✦ Just Posted</span>
                            ${offered?'<span class="badge-offered">✓ Offer Submitted</span>':''}
                        </div>
                    </div>
                    <div class="meta-strip">
                        ${dist!==null?`<span class="distance-pill">📍 ${dist} mi</span>`:''}
                        ${job.vehicle?`<span class="vehicle-badge">${job.vehicle.year} ${job.vehicle.make} ${job.vehicle.model}</span>`:''}
                        ${job.preferred_date?`<span class="meta-item">📅 ${job.preferred_date}${job.preferred_time?' · '+job.preferred_time:''}</span>`:''}
                    </div>
                    <p class="job-desc">${job.description}</p>
                    <div class="action-row">
                        ${!offered?`<a href="${job.show_url}" class="btn-offer">💰 Submit Offer</a>`:''}
                        <a href="${job.show_url}" class="btn-view">Details →</a>
                    </div>
                </div>
                <div style="text-align:right;min-width:90px;">
                    <div class="job-budget">${job.budget_label}</div>
                    <div style="font-size:.7rem;color:var(--text-tertiary);margin-top:.4rem;">just now</div>
                </div>
            </div>
        </div>`;
    }

    function addJob(job) {
        const dist = haversine(CFG.lat,CFG.lng,job.latitude,job.longitude);
        if (dist!==null && dist > CFG.radius) return;
        if (document.getElementById(`job-${job.id}`)) return;
        const empty = document.getElementById('emptyState');
        if (empty) empty.remove();
        document.getElementById('jobList').insertAdjacentHTML('afterbegin', buildCard(job));
        const lbl = document.getElementById('jobCountLabel');
        lbl.textContent = (parseInt(lbl.textContent)||0)+1+' near you';
        toast({icon:'🔔', title:'New Job Posted!', message:`${job.service_type} · ${job.budget_label}${dist?' · '+dist+' mi away':''}`, url:job.show_url});
    }

    function removeJob(id) {
        const el = document.getElementById(`job-${id}`);
        if (!el) return;
        el.classList.add('removing');
        setTimeout(()=>el.remove(), 450);
    }

    async function poll() {
        try {
            const p = new URLSearchParams({since:lastPoll,lat:CFG.lat||'',lng:CFG.lng||'',radius:CFG.radius});
            const r = await fetch(`/api/realtime/jobs/live?${p}`,{headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CFG.csrf}});
            if (!r.ok) {
                console.warn('[JobBoard] Poll HTTP error:', r.status);
                setLive(false); return;
            }
            const d = await r.json();
            setLive(true);
            lastPoll = d.server_time - 2; // 2s overlap safety margin
            console.log('[JobBoard] Poll ok — new:', (d.new_jobs||[]).length, 'closed:', (d.closed_job_ids||[]).length);
            (d.new_jobs||[]).forEach(addJob);
            (d.closed_job_ids||[]).forEach(removeJob);
        } catch(e) { console.error('[JobBoard] Poll error:', e); setLive(false); }
    }

    function init() {
        if (window.Echo) {
            try {
                Echo.channel('job-board')
                    .listen('.new-job', addJob)
                    .listen('.job-status-changed', d => removeJob(d.job_id));
                Echo.private(`provider.${CFG.userId}`)
                    .listen('.offer-status-changed', d => {
                        if (d.new_status==='accepted') toast({icon:'🎉',title:'Offer Accepted!',message:`Your $${d.offered_price} offer for ${d.service_type} was accepted!`});
                    });
                useEcho = true; setLive(true);
                pollTimer = setInterval(poll, 30000); // heartbeat
                return;
            } catch(e) { console.warn('[JobBoard] Echo unavailable, using polling'); }
        }
        setLive(true);
        poll();
        pollTimer = setInterval(poll, CFG.pollInterval);
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('visibilitychange', ()=>{
        if (document.hidden) { clearInterval(pollTimer); }
        else { lastPoll = Math.floor(Date.now()/1000)-30; clearInterval(pollTimer); init(); }
    });
})();
</script>
@endsection