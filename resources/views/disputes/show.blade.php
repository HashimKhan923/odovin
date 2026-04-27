@extends('layouts.app')
@section('title', 'Dispute ' . $dispute->reference)
@section('content')
<style>
.ds-wrap { max-width:820px;margin:0 auto;padding:2rem 1.5rem; }
.back-link { display:inline-flex;align-items:center;gap:.5rem;color:var(--accent-cyan);text-decoration:none;font-size:.875rem;font-weight:600;margin-bottom:1.5rem; }
.back-link:hover { gap:.875rem; }
.hero { background:var(--card-bg);border:1px solid var(--border-color);border-radius:18px;padding:1.75rem;margin-bottom:1.25rem;position:relative;overflow:hidden; }
.hero::before { content:'';position:absolute;top:0;left:0;right:0;height:3px; }
.status-open .hero::before          { background:var(--accent-warning); }
.status-under_review .hero::before  { background:linear-gradient(90deg,var(--accent-cyan),#a855f7); }
.status-resolved_consumer .hero::before,.status-resolved_provider .hero::before,.status-resolved_split .hero::before { background:var(--accent-green); }
.status-closed .hero::before        { background:rgba(255,255,255,.1); }
.hero-ref { font-size:.72rem;color:var(--text-tertiary);font-family:'Chakra Petch',sans-serif;margin-bottom:.35rem; }
.hero-title { font-family:'Orbitron',sans-serif;font-size:1.2rem;font-weight:800;margin-bottom:.625rem; }
.status-pill { display:inline-flex;align-items:center;gap:.35rem;padding:.28rem .875rem;border-radius:20px;font-size:.72rem;font-weight:700; }
.card { background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;padding:1.5rem;margin-bottom:1.25rem; }
.card-title { font-family:'Orbitron',sans-serif;font-size:.85rem;font-weight:700;margin-bottom:1rem;padding-bottom:.75rem;border-bottom:1px solid var(--border-color); }

/* Thread */
.thread { display:flex;flex-direction:column;gap:1rem;margin-bottom:1.25rem; }
.msg { display:flex;gap:.875rem;align-items:flex-start; }
.msg.admin .bubble { background:rgba(168,85,247,.08);border-color:rgba(168,85,247,.25); }
.msg.provider .bubble { background:rgba(0,212,255,.06);border-color:rgba(0,212,255,.2); }
.msg.consumer .bubble { background:rgba(0,255,170,.05);border-color:rgba(0,255,170,.2); }
.msg-avatar { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:800;flex-shrink:0;margin-top:.25rem; }
.bubble { flex:1;padding:1rem 1.125rem;border:1px solid var(--border-color);border-radius:14px; }
.bubble-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem; }
.bubble-role { font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em; }
.bubble-time { font-size:.7rem;color:var(--text-tertiary); }
.bubble-body { font-size:.875rem;color:var(--text-secondary);line-height:1.7;white-space:pre-wrap; }
.bubble-attachments { display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.75rem; }
.attach-thumb { width:60px;height:60px;border-radius:8px;object-fit:cover;border:1px solid var(--border-color);cursor:pointer; }
.attach-pdf { display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .75rem;background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);border-radius:8px;color:var(--accent-cyan);font-size:.75rem;text-decoration:none; }

/* Reply form */
.reply-card { background:var(--card-bg);border:1px solid rgba(0,212,255,.2);border-radius:14px;padding:1.5rem; }
.field-label { display:block;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);margin-bottom:.45rem; }
.field-textarea { width:100%;padding:.75rem 1rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);font-family:'Chakra Petch',sans-serif;font-size:.875rem;resize:vertical;min-height:90px;outline:none;transition:border-color .2s; }
.field-textarea:focus { border-color:var(--accent-cyan); }
.btn-send { padding:.75rem 1.5rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.78rem;cursor:pointer;transition:all .2s; }
.btn-send:hover { transform:translateY(-1px); }
.evidence-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:.625rem; }
.evidence-thumb { border-radius:10px;overflow:hidden;aspect-ratio:1;cursor:pointer; }
.evidence-thumb img { width:100%;height:100%;object-fit:cover;transition:transform .3s; }
.evidence-thumb:hover img { transform:scale(1.06); }
.resolution-box { background:rgba(0,255,170,.06);border:1px solid rgba(0,255,170,.25);border-radius:14px;padding:1.5rem;margin-bottom:1.25rem; }
</style>

<div class="ds-wrap status-{{ $dispute->status }}">
    <a href="{{ route('jobs.show', $dispute->job) }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Job
    </a>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-ref">{{ $dispute->reference }} · Raised {{ $dispute->created_at->diffForHumans() }}</div>
        <div class="hero-title">{{ $dispute->reasonLabel() }}</div>
        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <span class="status-pill" style="background:{{ $dispute->statusColor() }}1a;color:{{ $dispute->statusColor() }};border:1px solid {{ $dispute->statusColor() }}44;">
                {{ $dispute->statusLabel() }}
            </span>
            <span style="font-size:.8rem;color:var(--text-tertiary);">
                Job #{{ $dispute->job->job_number }} · {{ $dispute->job->service_type }}
                · <strong style="color:var(--text-primary);">{{ $dispute->job->escrow?->formattedAmount() }}</strong> frozen
            </span>
        </div>
    </div>

    {{-- Resolution box if resolved --}}
    @if($dispute->isResolved())
    <div class="resolution-box">
        <div style="font-family:'Orbitron',sans-serif;font-size:.875rem;font-weight:700;color:var(--accent-green);margin-bottom:.5rem;">✅ Resolved</div>
        <div style="font-size:.875rem;font-weight:700;color:var(--text-primary);margin-bottom:.5rem;">{{ $dispute->resolutionLabel() }}</div>
        <div style="font-size:.82rem;color:var(--text-secondary);line-height:1.7;">{{ $dispute->resolution_notes }}</div>
        <div style="font-size:.75rem;color:var(--text-tertiary);margin-top:.625rem;">Resolved {{ $dispute->resolved_at?->format('M d, Y · g:i A') }}</div>
    </div>
    @endif

    {{-- Message thread --}}
    <div class="card">
        <div class="card-title">💬 Dispute Thread ({{ $dispute->message_count }} messages)</div>
        <div class="thread">
            @foreach($dispute->messages->where('is_internal', false) as $msg)
            <div class="msg {{ $msg->sender_role }}">
                <div class="msg-avatar" style="background:{{ $msg->roleColor() }}22;color:{{ $msg->roleColor() }};">
                    {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                </div>
                <div class="bubble">
                    <div class="bubble-header">
                        <span class="bubble-role" style="color:{{ $msg->roleColor() }};">{{ $msg->roleLabel() }} · {{ $msg->user->name }}</span>
                        <span class="bubble-time">{{ $msg->created_at->format('M d · g:i A') }}</span>
                    </div>
                    <div class="bubble-body">{{ $msg->message }}</div>
                    @if($msg->attachments)
                    <div class="bubble-attachments">
                        @foreach($msg->attachmentUrls() as $url)
                            @if(str_ends_with(strtolower($url), '.pdf'))
                            <a href="{{ $url }}" target="_blank" class="attach-pdf">📄 PDF</a>
                            @else
                            <img src="{{ $url }}" class="attach-thumb" onclick="openEvidenceLightbox('{{ $url }}')">
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Reply form --}}
    @if($dispute->isActive())
    <div class="reply-card">
        <div class="card-title" style="font-family:'Orbitron',sans-serif;font-size:.85rem;font-weight:700;margin-bottom:1rem;">Reply to Thread</div>

        @php
            $isProvider = auth()->user()->serviceProvider &&
                ($dispute->job->acceptedOffer?->serviceProvider?->id === auth()->user()->serviceProvider->id
                || $dispute->job->assigned_provider_id === auth()->user()->serviceProvider?->id);
        @endphp

        <form method="POST"
              action="{{ $isProvider ? route('disputes.provider-response', $dispute) : route('disputes.message', $dispute) }}"
              enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:.875rem;">
                <label class="field-label">Your message</label>
                <textarea name="message" class="field-textarea" placeholder="Add to the thread..." required minlength="5"></textarea>
            </div>
            @if($isProvider)
            <div style="margin-bottom:.875rem;">
                <label class="field-label">Attach evidence (optional)</label>
                <input type="file" name="evidence[]" accept="image/*,.pdf" multiple
                       style="font-size:.8rem;color:var(--text-secondary);">
            </div>
            @else
            <div style="margin-bottom:.875rem;">
                <label class="field-label">Attach files (optional)</label>
                <input type="file" name="attachments[]" accept="image/*,.pdf" multiple
                       style="font-size:.8rem;color:var(--text-secondary);">
            </div>
            @endif
            <button type="submit" class="btn-send">Send Message →</button>
        </form>
    </div>
    @endif
</div>

@include('partials.job_evidence', ['record' => \App\Models\ServiceRecord::where('job_post_id', $dispute->job_post_id)->first()])

{{-- Lightbox --}}
@once
<div id="evidenceLightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeEvidenceLightbox()">
    <button onclick="closeEvidenceLightbox()" style="position:absolute;top:1.5rem;right:1.5rem;background:rgba(255,255,255,.1);border:none;border-radius:50%;width:44px;height:44px;color:#fff;font-size:1.25rem;cursor:pointer;">✕</button>
    <img id="evidenceLightboxImg" src="" style="max-width:92vw;max-height:90vh;border-radius:12px;object-fit:contain;">
</div>
<script>
function openEvidenceLightbox(src) { document.getElementById('evidenceLightboxImg').src=src; document.getElementById('evidenceLightbox').style.display='flex'; }
function closeEvidenceLightbox() { document.getElementById('evidenceLightbox').style.display='none'; }
document.addEventListener('keydown', e => { if(e.key==='Escape') closeEvidenceLightbox(); });
</script>
@endonce
@endsection