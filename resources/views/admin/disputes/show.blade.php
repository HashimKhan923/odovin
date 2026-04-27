@extends('admin.layouts.app')
@section('title', 'Dispute ' . $dispute->reference)
@section('content')
<style>
.dp-grid { display:grid;grid-template-columns:1fr 380px;gap:1.5rem;align-items:start; }
@media(max-width:900px){.dp-grid{grid-template-columns:1fr}}
.panel { background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:1.5rem;margin-bottom:1.25rem; }
.panel-title { font-family:'Orbitron',sans-serif;font-size:.85rem;font-weight:700;margin-bottom:1.1rem;padding-bottom:.75rem;border-bottom:1px solid var(--border-color); }
.dl { display:grid;grid-template-columns:1fr 1fr;gap:.75rem 1.25rem; }
.dl-item label { font-size:.7rem;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:.2rem; }
.dl-item span { font-size:.875rem;font-weight:600;color:var(--text-primary); }
.thread { display:flex;flex-direction:column;gap:.875rem; }
.msg { display:flex;gap:.75rem;align-items:flex-start; }
.msg.admin .bubble { background:rgba(168,85,247,.07);border-color:rgba(168,85,247,.25); }
.msg.provider .bubble { background:rgba(0,212,255,.06);border-color:rgba(0,212,255,.2); }
.msg.consumer .bubble { background:rgba(0,255,170,.05);border-color:rgba(0,255,170,.2); }
.msg.internal .bubble { background:rgba(255,170,0,.06);border-color:rgba(255,170,0,.2); }
.msg-av { width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:800;flex-shrink:0;margin-top:.2rem; }
.bubble { flex:1;padding:.875rem 1rem;border:1px solid var(--border-color);border-radius:12px; }
.bh { display:flex;align-items:center;justify-content:space-between;margin-bottom:.375rem; }
.bh-role { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em; }
.bh-time { font-size:.68rem;color:var(--text-tertiary); }
.bh-internal { font-size:.68rem;background:rgba(255,170,0,.12);color:var(--accent-warning);border:1px solid rgba(255,170,0,.25);border-radius:6px;padding:.1rem .5rem; }
.bb { font-size:.82rem;color:var(--text-secondary);line-height:1.65;white-space:pre-wrap; }
.field-input,.field-select,.field-textarea { width:100%;padding:.7rem 1rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);font-family:'Chakra Petch',sans-serif;font-size:.875rem;outline:none;transition:border-color .2s;box-sizing:border-box; }
.field-input:focus,.field-select:focus,.field-textarea:focus { border-color:var(--accent-cyan); }
.field-select option { background:#121827; }
.field-textarea { resize:vertical;min-height:80px;line-height:1.6; }
.field-label { display:block;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);margin-bottom:.4rem; }
.btn-resolve { width:100%;padding:.875rem;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green));border:none;border-radius:10px;color:#000;font-family:'Orbitron',sans-serif;font-weight:800;font-size:.8rem;cursor:pointer;transition:all .3s; }
.btn-resolve:hover { transform:translateY(-1px);box-shadow:0 4px 15px rgba(0,212,255,.3); }
.btn-assign { width:100%;padding:.75rem;background:rgba(168,85,247,.12);border:1px solid rgba(168,85,247,.3);border-radius:10px;color:#c084fc;font-family:'Orbitron',sans-serif;font-weight:700;font-size:.78rem;cursor:pointer;transition:all .2s;margin-bottom:.875rem; }
.btn-assign:hover { background:rgba(168,85,247,.2); }
.evidence-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:.5rem;margin-top:.75rem; }
.ev-thumb { border-radius:8px;overflow:hidden;aspect-ratio:1;cursor:pointer; }
.ev-thumb img { width:100%;height:100%;object-fit:cover;transition:transform .25s; }
.ev-thumb:hover img { transform:scale(1.06); }
.resolution-done { background:rgba(0,255,170,.06);border:1px solid rgba(0,255,170,.2);border-radius:12px;padding:1.25rem; }
</style>

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
    <div>
        <a href="{{ route('admin.disputes.index') }}" class="btn btn-secondary btn-sm">← Back to Disputes</a>
    </div>
    <div style="display:flex;align-items:center;gap:.75rem;">
        <span style="display:inline-flex;padding:.28rem .875rem;border-radius:20px;font-size:.72rem;font-weight:700;background:{{ $dispute->statusColor() }}1a;color:{{ $dispute->statusColor() }};border:1px solid {{ $dispute->statusColor() }}33;">{{ $dispute->statusLabel() }}</span>
        <span style="font-size:.78rem;color:var(--text-tertiary);">{{ $dispute->reference }}</span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:1.5rem;">✓ {{ session('success') }}</div>
@endif

<div class="dp-grid">

    {{-- Left: thread + evidence --}}
    <div>
        {{-- Dispute details --}}
        <div class="panel">
            <div class="panel-title">📋 Dispute Details</div>
            <div class="dl">
                <div class="dl-item"><label>Reference</label><span style="color:var(--accent-cyan);font-family:'Orbitron',sans-serif;">{{ $dispute->reference }}</span></div>
                <div class="dl-item"><label>Reason</label><span>{{ $dispute->reasonLabel() }}</span></div>
                <div class="dl-item"><label>Raised By</label><span>{{ $dispute->raisedBy->name }} ({{ $dispute->raised_by_role }})</span></div>
                <div class="dl-item"><label>Job</label><span>#{{ $dispute->job->job_number }}</span></div>
                <div class="dl-item"><label>Service</label><span>{{ $dispute->job->service_type }}</span></div>
                <div class="dl-item"><label>Amount Frozen</label><span style="color:#ff8099;font-family:'Orbitron',sans-serif;">{{ $dispute->escrow?->formattedAmount() ?? '—' }}</span></div>
                <div class="dl-item"><label>Consumer</label><span>{{ $dispute->job->user->name }}</span></div>
                <div class="dl-item"><label>Provider</label><span>{{ $dispute->job->acceptedOffer?->serviceProvider?->business_name ?? $dispute->job->assignedProvider?->business_name ?? '—' }}</span></div>
            </div>
        </div>

        {{-- Evidence --}}
        @if($dispute->consumer_evidence || $dispute->provider_evidence)
        <div class="panel">
            <div class="panel-title">📎 Submitted Evidence</div>
            @if($dispute->consumer_evidence)
            <div style="margin-bottom:1rem;">
                <div style="font-size:.75rem;font-weight:700;color:var(--accent-green);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">Consumer Evidence</div>
                <div class="evidence-grid">
                    @foreach($dispute->consumer_evidence as $path)
                    @php $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path); @endphp
                    @if(str_ends_with(strtolower($path),'.pdf'))
                    <a href="{{ $url }}" target="_blank" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .75rem;background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);border-radius:8px;color:var(--accent-cyan);font-size:.75rem;text-decoration:none;">📄 PDF</a>
                    @else
                    <div class="ev-thumb" onclick="openEvidenceLightbox('{{ $url }}')"><img src="{{ $url }}"></div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
            @if($dispute->provider_evidence)
            <div>
                <div style="font-size:.75rem;font-weight:700;color:var(--accent-cyan);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">Provider Evidence</div>
                <div class="evidence-grid">
                    @foreach($dispute->provider_evidence as $path)
                    @php $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path); @endphp
                    @if(str_ends_with(strtolower($path),'.pdf'))
                    <a href="{{ $url }}" target="_blank" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .75rem;background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);border-radius:8px;color:var(--accent-cyan);font-size:.75rem;text-decoration:none;">📄 PDF</a>
                    @else
                    <div class="ev-thumb" onclick="openEvidenceLightbox('{{ $url }}')"><img src="{{ $url }}"></div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Job before/after evidence --}}
        @include('partials.job_evidence', ['record' => \App\Models\ServiceRecord::where('job_post_id', $dispute->job_post_id)->first()])

        {{-- Thread --}}
        <div class="panel">
            <div class="panel-title">💬 Thread ({{ $dispute->message_count }})</div>
            <div class="thread">
                @foreach($dispute->messages as $msg)
                <div class="msg {{ $msg->sender_role }} {{ $msg->is_internal ? 'internal' : '' }}">
                    <div class="msg-av" style="background:{{ $msg->roleColor() }}22;color:{{ $msg->roleColor() }};">{{ strtoupper(substr($msg->user->name,0,1)) }}</div>
                    <div class="bubble">
                        <div class="bh">
                            <span class="bh-role" style="color:{{ $msg->roleColor() }};">{{ $msg->roleLabel() }} · {{ $msg->user->name }}</span>
                            <div style="display:flex;align-items:center;gap:.5rem;">
                                @if($msg->is_internal)<span class="bh-internal">Internal</span>@endif
                                <span class="bh-time">{{ $msg->created_at->format('M d · g:i A') }}</span>
                            </div>
                        </div>
                        <div class="bb">{{ $msg->message }}</div>
                        @if($msg->attachments)
                        <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.625rem;">
                            @foreach($msg->attachmentUrls() as $url)
                                @if(str_ends_with(strtolower($url),'.pdf'))
                                <a href="{{ $url }}" target="_blank" style="font-size:.75rem;color:var(--accent-cyan);">📄 PDF ↗</a>
                                @else
                                <img src="{{ $url }}" style="width:56px;height:56px;border-radius:8px;object-fit:cover;cursor:pointer;" onclick="openEvidenceLightbox('{{ $url }}')">
                                @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Admin message form --}}
            @if($dispute->isActive())
            <form method="POST" action="{{ route('admin.disputes.message', $dispute) }}" style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border-color);">
                @csrf
                <div style="margin-bottom:.75rem;">
                    <label class="field-label">Admin message</label>
                    <textarea name="message" class="field-textarea" placeholder="Visible to both parties..." required></textarea>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
                    <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:var(--text-secondary);cursor:pointer;">
                        <input type="checkbox" name="is_internal" value="1" style="accent-color:var(--accent-warning);"> Internal note only
                    </label>
                    <button type="submit" class="btn btn-secondary btn-sm">Send Message →</button>
                </div>
            </form>
            @endif
        </div>
    </div>

    {{-- Right: actions --}}
    <div style="position:sticky;top:5rem;">

        {{-- Assign --}}
        @if($dispute->isOpen())
        <div class="panel">
            <form method="POST" action="{{ route('admin.disputes.assign', $dispute) }}">
                @csrf
                <button type="submit" class="btn-assign">⚡ Assign to Me & Start Review</button>
            </form>
            <div style="font-size:.75rem;color:var(--text-tertiary);text-align:center;">Marks as Under Review and notifies both parties</div>
        </div>
        @endif

        {{-- Resolve --}}
        @if($dispute->isActive())
        <div class="panel">
            <div class="panel-title">✅ Resolve Dispute</div>
            <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}">
                @csrf
                <div style="margin-bottom:.875rem;">
                    <label class="field-label">Decision <span style="color:#ff8099;">*</span></label>
                    <select name="resolution" class="field-select" required onchange="togglePartialAmount(this.value)">
                        <option value="">Select outcome...</option>
                        <option value="full_refund">Full Refund → Consumer</option>
                        <option value="partial_refund">Partial Refund (split)</option>
                        <option value="release_to_provider">Release Payment → Provider</option>
                        <option value="no_action">Close — No Action</option>
                    </select>
                </div>
                <div id="partialAmountField" style="display:none;margin-bottom:.875rem;">
                    <label class="field-label">Refund Amount ($) <span style="color:#ff8099;">*</span></label>
                    <input type="number" name="resolution_amount" class="field-input" step="0.01" min="1"
                        placeholder="Amount to refund to consumer"
                        max="{{ $dispute->escrow ? $dispute->escrow->amount / 100 : 9999 }}">
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-top:.3rem;">Remaining goes to provider after platform fee.</div>
                </div>
                <div style="margin-bottom:.875rem;">
                    <label class="field-label">Resolution Notes <span style="color:#ff8099;">*</span></label>
                    <textarea name="resolution_notes" class="field-textarea" required placeholder="Explain your decision to both parties..."></textarea>
                </div>
                <button type="submit" class="btn-resolve"
                    onclick="return confirm('Resolve this dispute? This will execute the financial action immediately.')">
                    Resolve & Execute →
                </button>
            </form>
        </div>
        @elseif($dispute->isResolved())
        <div class="resolution-done">
            <div style="font-family:'Orbitron',sans-serif;font-size:.85rem;font-weight:700;color:var(--accent-green);margin-bottom:.5rem;">✅ Resolved</div>
            <div style="font-size:.875rem;font-weight:700;color:var(--text-primary);margin-bottom:.5rem;">{{ $dispute->resolutionLabel() }}</div>
            <div style="font-size:.82rem;color:var(--text-secondary);line-height:1.65;">{{ $dispute->resolution_notes }}</div>
            <div style="font-size:.75rem;color:var(--text-tertiary);margin-top:.625rem;">{{ $dispute->resolved_at?->format('M d, Y · g:i A') }} by {{ $dispute->resolver?->name }}</div>
        </div>
        @endif

        {{-- Job info quick panel --}}
        <div class="panel">
            <div class="panel-title">💼 Job Info</div>
            <div style="font-size:.82rem;color:var(--text-secondary);line-height:1.8;">
                <div><span style="color:var(--text-tertiary);">Job:</span> #{{ $dispute->job->job_number }}</div>
                <div><span style="color:var(--text-tertiary);">Service:</span> {{ $dispute->job->service_type }}</div>
                <div><span style="color:var(--text-tertiary);">Vehicle:</span> {{ $dispute->job->vehicle?->year }} {{ $dispute->job->vehicle?->make }} {{ $dispute->job->vehicle?->model }}</div>
                <div><span style="color:var(--text-tertiary);">Work Status:</span> {{ ucfirst(str_replace('_',' ',$dispute->job->work_status ?? 'pending')) }}</div>
                <div><span style="color:var(--text-tertiary);">Escrow:</span> {{ $dispute->escrow?->formattedAmount() }} <span style="color:#ff8099;">(frozen)</span></div>
            </div>
            <a href="{{ route('admin.jobs.show', $dispute->job) }}"
               style="display:inline-flex;align-items:center;gap:.4rem;margin-top:.875rem;padding:.5rem 1rem;background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);border-radius:8px;color:var(--accent-cyan);font-size:.78rem;text-decoration:none;">
               View Full Job →
            </a>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div id="evidenceLightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeEvidenceLightbox()">
    <button onclick="closeEvidenceLightbox()" style="position:absolute;top:1.5rem;right:1.5rem;background:rgba(255,255,255,.1);border:none;border-radius:50%;width:44px;height:44px;color:#fff;font-size:1.25rem;cursor:pointer;">✕</button>
    <img id="evidenceLightboxImg" src="" style="max-width:92vw;max-height:90vh;border-radius:12px;object-fit:contain;">
</div>
<script>
function openEvidenceLightbox(src) { document.getElementById('evidenceLightboxImg').src=src; document.getElementById('evidenceLightbox').style.display='flex'; }
function closeEvidenceLightbox() { document.getElementById('evidenceLightbox').style.display='none'; }
function togglePartialAmount(val) { document.getElementById('partialAmountField').style.display = val === 'partial_refund' ? 'block' : 'none'; }
document.addEventListener('keydown', e => { if(e.key==='Escape') closeEvidenceLightbox(); });
</script>
@endsection