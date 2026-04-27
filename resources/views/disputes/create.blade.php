@extends('layouts.app')
@section('title', 'Raise a Dispute — Job #' . $job->job_number)
@section('content')
<style>
.dp-wrap { max-width:700px; margin:0 auto; padding:2rem 1.5rem; }
.back-link { display:inline-flex;align-items:center;gap:.5rem;color:var(--accent-cyan);text-decoration:none;font-size:.875rem;font-weight:600;margin-bottom:1.5rem; }
.back-link:hover { gap:.875rem; }
.warn-box { background:rgba(255,51,102,.06);border:1px solid rgba(255,51,102,.25);border-radius:14px;padding:1.25rem 1.5rem;margin-bottom:1.5rem; }
.warn-title { font-weight:700;color:#ff8099;font-size:.9rem;margin-bottom:.5rem;display:flex;align-items:center;gap:.5rem; }
.warn-body { font-size:.825rem;color:var(--text-secondary);line-height:1.7; }
.form-card { background:var(--card-bg);border:1px solid var(--border-color);border-radius:18px;padding:2rem; }
.card-title { font-family:'Orbitron',sans-serif;font-size:1rem;font-weight:700;margin-bottom:1.5rem;padding-bottom:.875rem;border-bottom:1px solid var(--border-color); }
.field-group { margin-bottom:1.25rem; }
.field-label { display:block;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);margin-bottom:.45rem; }
.field-label span { color:#ff8099; }
.field-input,.field-select,.field-textarea { width:100%;padding:.75rem 1rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);font-family:'Chakra Petch',sans-serif;font-size:.875rem;transition:border-color .2s; }
.field-input:focus,.field-select:focus,.field-textarea:focus { outline:none;border-color:var(--accent-cyan);box-shadow:0 0 0 3px rgba(0,212,255,.08); }
.field-select option { background:#121827; }
.field-textarea { resize:vertical;min-height:130px;line-height:1.6; }
.field-hint { font-size:.72rem;color:var(--text-tertiary);margin-top:.35rem; }
.reasons-grid { display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem; }
.reason-opt { position:relative; }
.reason-opt input { position:absolute;opacity:0;width:0;height:0; }
.reason-label { display:flex;align-items:center;gap:.75rem;padding:.875rem 1rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:12px;cursor:pointer;transition:all .2s;font-size:.82rem;color:var(--text-secondary); }
.reason-opt input:checked + .reason-label { border-color:var(--accent-cyan);background:rgba(0,212,255,.1);color:var(--text-primary); }
.reason-label:hover { border-color:rgba(0,212,255,.25); }
.reason-icon { font-size:1.2rem;flex-shrink:0; }
.upload-zone { border:2px dashed rgba(0,212,255,.2);border-radius:12px;padding:1.5rem;text-align:center;cursor:pointer;transition:all .3s;position:relative; }
.upload-zone:hover { border-color:var(--accent-cyan);background:rgba(0,212,255,.04); }
.upload-zone input { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
.btn-submit { width:100%;padding:1rem;background:linear-gradient(135deg,#ff3366,#ff6600);border:none;border-radius:12px;color:#fff;font-family:'Orbitron',sans-serif;font-weight:800;font-size:.875rem;cursor:pointer;transition:all .3s;margin-top:.5rem; }
.btn-submit:hover { transform:translateY(-1px);box-shadow:0 4px 20px rgba(255,51,102,.35); }
.job-strip { background:rgba(0,212,255,.04);border:1px solid rgba(0,212,255,.12);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;flex-wrap:wrap;gap:1.5rem;font-size:.82rem; }
.js-item label { font-size:.7rem;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:.2rem; }
.js-item span { font-weight:700;color:var(--text-primary); }
</style>

<div class="dp-wrap">
    <a href="{{ route('jobs.show', $job) }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Job
    </a>

    <div class="warn-box">
        <div class="warn-title">⚠ Before you raise a dispute</div>
        <div class="warn-body">
            Filing a dispute will <strong>freeze your payment</strong> in escrow until our team reviews both sides.
            The provider will be notified and asked to respond. We aim to resolve disputes within <strong>48–72 hours</strong>.
            If possible, try messaging the provider first to resolve the issue directly.
        </div>
    </div>

    @if($errors->any())
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">
        @foreach($errors->all() as $e)<div>✕ {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="job-strip">
        <div class="js-item"><label>Job</label><span>#{{ $job->job_number }}</span></div>
        <div class="js-item"><label>Service</label><span>{{ $job->service_type }}</span></div>
        <div class="js-item"><label>Amount Held</label><span>{{ $job->escrow->formattedAmount() }}</span></div>
        @if($job->vehicle)<div class="js-item"><label>Vehicle</label><span>{{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}</span></div>@endif
    </div>

    <div class="form-card">
        <div class="card-title">⚖ Raise a Dispute</div>

        <form method="POST" action="{{ route('disputes.store', $job) }}" enctype="multipart/form-data">
            @csrf

            <div class="field-group">
                <label class="field-label">What is the issue? <span>*</span></label>
                <div class="reasons-grid">
                    @foreach($reasons as $value => $label)
                    @php
                        $icons = ['work_not_done'=>'🚫','poor_quality'=>'💔','no_show'=>'🕐','overcharged'=>'💸','damage'=>'🔨','other'=>'📋'];
                    @endphp
                    <div class="reason-opt">
                        <input type="radio" name="reason_code" id="reason_{{ $value }}" value="{{ $value }}"
                            {{ old('reason_code') === $value ? 'checked' : '' }} required>
                        <label class="reason-label" for="reason_{{ $value }}">
                            <span class="reason-icon">{{ $icons[$value] ?? '📋' }}</span>
                            {{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="field-group">
                <label class="field-label" for="description">Describe what happened <span>*</span></label>
                <textarea name="description" id="description" class="field-textarea"
                    placeholder="Be specific — what was promised, what happened, when it happened, and what outcome you're expecting. Min 30 characters."
                    minlength="30" maxlength="3000" required>{{ old('description') }}</textarea>
                <p class="field-hint">The provider and our admin team will see this.</p>
            </div>

            <div class="field-group">
                <label class="field-label">Supporting Evidence (optional)</label>
                <div class="upload-zone">
                    <input type="file" name="evidence[]" accept="image/*,.pdf" multiple>
                    <div style="font-size:1.75rem;margin-bottom:.5rem;">📎</div>
                    <div style="font-size:.82rem;font-weight:600;color:var(--text-secondary);">Upload photos, screenshots, or documents</div>
                    <div style="font-size:.72rem;color:var(--text-tertiary);margin-top:.25rem;">JPG, PNG, PDF — max 10MB each</div>
                </div>
                <p class="field-hint">Photos of the work, screenshots of messages, receipts — anything that supports your case.</p>
            </div>

            <button type="submit" class="btn-submit"
                onclick="return confirm('Raise this dispute? Payment will be frozen until resolved.')">
                ⚖ Submit Dispute
            </button>
            <p style="font-size:.72rem;color:var(--text-tertiary);text-align:center;margin-top:.75rem;">
                Our team reviews all disputes fairly. Both parties will be heard before any decision is made.
            </p>
        </form>
    </div>
</div>
@endsection