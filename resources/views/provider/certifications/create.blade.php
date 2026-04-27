@extends('provider.layouts.app')
@section('title', 'Upload Certification')
@section('content')
<style>
.cert-form-wrap { max-width: 680px; }
.back-link { display:inline-flex; align-items:center; gap:.5rem; color:var(--accent-cyan); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:1.5rem; transition:gap .2s; }
.back-link:hover { gap:.875rem; }
.form-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:18px; padding:2rem; }
.form-card-title { font-family:'Orbitron',sans-serif; font-size:1rem; font-weight:700; margin-bottom:1.5rem; padding-bottom:.875rem; border-bottom:1px solid var(--border-color); }
.field-group { margin-bottom:1.25rem; }
.field-label { display:block; font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--text-tertiary); margin-bottom:.45rem; }
.field-label span { color:#ff8099; }
.field-input, .field-select { width:100%; padding:.75rem 1rem; background:rgba(0,212,255,.04); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); font-family:'Chakra Petch',sans-serif; font-size:.875rem; transition:border-color .2s; }
.field-input:focus, .field-select:focus { outline:none; border-color:var(--accent-cyan); box-shadow:0 0 0 3px rgba(0,212,255,.08); }
.field-select option { background:rgba(18,24,39,1); }
.field-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
@media(max-width:560px) { .field-grid { grid-template-columns:1fr; } }
.field-hint { font-size:.72rem; color:var(--text-tertiary); margin-top:.35rem; }
.upload-zone { border:2px dashed rgba(0,212,255,.25); border-radius:14px; padding:2.5rem 1.5rem; text-align:center; cursor:pointer; transition:all .3s; position:relative; }
.upload-zone:hover, .upload-zone.drag-over { border-color:var(--accent-cyan); background:rgba(0,212,255,.04); }
.upload-zone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
.upload-icon { font-size:2.5rem; margin-bottom:.75rem; }
.upload-title { font-family:'Orbitron',sans-serif; font-size:.875rem; font-weight:700; color:var(--text-primary); margin-bottom:.35rem; }
.upload-sub { font-size:.78rem; color:var(--text-tertiary); }
.file-preview { display:none; align-items:center; gap:1rem; padding:1rem; background:rgba(0,212,255,.06); border:1px solid rgba(0,212,255,.2); border-radius:10px; margin-top:1rem; }
.file-preview-icon { font-size:1.75rem; }
.file-preview-name { font-size:.82rem; font-weight:600; color:var(--text-primary); }
.file-preview-size { font-size:.72rem; color:var(--text-tertiary); }
.cert-types { display:grid; grid-template-columns:repeat(3,1fr); gap:.625rem; margin-bottom:1.5rem; }
.cert-type-btn { padding:.625rem .75rem; background:rgba(0,212,255,.04); border:1px solid var(--border-color); border-radius:10px; text-align:center; cursor:pointer; transition:all .2s; font-size:.75rem; color:var(--text-secondary); }
.cert-type-btn:hover, .cert-type-btn.selected { border-color:var(--accent-cyan); background:rgba(0,212,255,.1); color:var(--text-primary); }
.cert-type-icon { font-size:1.25rem; display:block; margin-bottom:.25rem; }
.btn-submit { width:100%; padding:1rem; background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); border:none; border-radius:12px; color:#000; font-family:'Orbitron',sans-serif; font-weight:800; font-size:.875rem; cursor:pointer; transition:all .3s; margin-top:.5rem; }
.btn-submit:hover { transform:translateY(-1px); box-shadow:0 4px 20px rgba(0,212,255,.35); }
.info-box { background:rgba(0,212,255,.05); border:1px solid rgba(0,212,255,.15); border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.5rem; font-size:.8rem; color:var(--text-secondary); line-height:1.7; }
</style>

<div class="cert-form-wrap">
    <a href="{{ route('provider.certifications.index') }}" class="back-link">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        My Certifications
    </a>

    <div class="info-box">
        📋 <strong>How it works:</strong> Upload your certificate document (PDF or image). Our team reviews it within 24 hours. Once approved, a <strong>✓ Verified</strong> badge appears on your profile and directory listing — building consumer trust and improving your ranking.
    </div>

    @if($errors->any())
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">
        @foreach($errors->all() as $e)<div>✕ {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="form-card">
        <div class="form-card-title">📎 Upload Certification</div>

        <form method="POST" action="{{ route('provider.certifications.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Quick-select common cert types --}}
            <div style="margin-bottom:1.5rem;">
                <div class="field-label" style="margin-bottom:.75rem;">Common Certification Types <span style="color:var(--text-tertiary);font-weight:400;text-transform:none;">(tap to fill)</span></div>
                <div class="cert-types">
                    @foreach([
                        ['🔧', 'ASE Certified',      'ASE',          'National Institute for Automotive Service Excellence'],
                        ['⚡', 'EV Specialist',       'EV',           'EV Certification Institute'],
                        ['🌿', 'EPA 609',             'EPA',          'Environmental Protection Agency'],
                        ['🏭', 'OEM Certified',       'OEM',          'Manufacturer Certification'],
                        ['🔩', 'I-CAR Certified',     'I-CAR',        'Inter-Industry Conference on Auto Collision Repair'],
                        ['📋', 'Other',               '',             ''],
                    ] as [$icon, $label, $issuer, $body])
                    <div class="cert-type-btn" onclick="fillType('{{ $label }}','{{ $body }}')">
                        <span class="cert-type-icon">{{ $icon }}</span>
                        {{ $label }}
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Cert name --}}
            <div class="field-group">
                <label class="field-label" for="name">Certification Name <span>*</span></label>
                <input type="text" id="name" name="name" class="field-input"
                    value="{{ old('name') }}" placeholder="e.g. ASE Master Technician" required maxlength="255">
            </div>

            {{-- Issuing body --}}
            <div class="field-group">
                <label class="field-label" for="issuing_body">Issuing Organization <span>*</span></label>
                <input type="text" id="issuing_body" name="issuing_body" class="field-input"
                    value="{{ old('issuing_body') }}" placeholder="e.g. ASE, EPA, Ford Motor Company" required maxlength="255">
            </div>

            {{-- Certificate number + dates --}}
            <div class="field-group">
                <label class="field-label" for="certificate_number">Certificate Number</label>
                <input type="text" id="certificate_number" name="certificate_number" class="field-input"
                    value="{{ old('certificate_number') }}" placeholder="Optional — shown on your profile" maxlength="100">
            </div>

            <div class="field-grid">
                <div class="field-group">
                    <label class="field-label" for="issued_at">Date Issued</label>
                    <input type="date" id="issued_at" name="issued_at" class="field-input"
                        value="{{ old('issued_at') }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="field-group">
                    <label class="field-label" for="expires_at">Expiry Date</label>
                    <input type="date" id="expires_at" name="expires_at" class="field-input"
                        value="{{ old('expires_at') }}">
                    <p class="field-hint">Leave blank if it never expires</p>
                </div>
            </div>

            {{-- File upload --}}
            <div class="field-group">
                <label class="field-label">Certificate Document <span>*</span></label>
                <div class="upload-zone" id="uploadZone">
                    <input type="file" name="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png,.webp"
                        onchange="previewFile(this)" required>
                    <div class="upload-icon">📄</div>
                    <div class="upload-title">Drop your file here or click to browse</div>
                    <div class="upload-sub">PDF, JPG, PNG or WebP — max 5MB</div>
                </div>
                <div class="file-preview" id="filePreview">
                    <span class="file-preview-icon" id="previewIcon">📄</span>
                    <div>
                        <div class="file-preview-name" id="previewName">filename.pdf</div>
                        <div class="file-preview-size" id="previewSize">—</div>
                    </div>
                    <button type="button" onclick="clearFile()" style="margin-left:auto;background:none;border:none;color:#ff8099;cursor:pointer;font-size:.875rem;">✕ Remove</button>
                </div>
            </div>

            {{-- Show on profile toggle --}}
            <div class="field-group" style="display:flex;align-items:center;gap:1rem;padding:.875rem 1rem;background:rgba(0,212,255,.04);border:1px solid var(--border-color);border-radius:10px;">
                <input type="hidden" name="show_on_profile" value="0">
                <input type="checkbox" id="show_on_profile" name="show_on_profile" value="1" checked
                    style="width:18px;height:18px;accent-color:var(--accent-cyan);cursor:pointer;">
                <div>
                    <label for="show_on_profile" style="font-size:.82rem;font-weight:600;color:var(--text-primary);cursor:pointer;display:block;">Show badge on profile once approved</label>
                    <div style="font-size:.72rem;color:var(--text-tertiary);">Consumers will see your certification badges when viewing your profile</div>
                </div>
            </div>

            <button type="submit" class="btn-submit">Submit for Review →</button>
            <p style="font-size:.72rem;color:var(--text-tertiary);text-align:center;margin-top:.75rem;">
                Your document is stored securely. Only our admin team can view it for verification purposes.
            </p>
        </form>
    </div>
</div>

<script>
function fillType(name, body) {
    document.getElementById('name').value = name;
    if (body) document.getElementById('issuing_body').value = body;
    document.querySelectorAll('.cert-type-btn').forEach(b => b.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
}

function previewFile(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const preview = document.getElementById('filePreview');
    document.getElementById('previewName').textContent = file.name;
    document.getElementById('previewSize').textContent = (file.size / 1024).toFixed(0) + ' KB';
    document.getElementById('previewIcon').textContent = file.type === 'application/pdf' ? '📄' : '🖼';
    preview.style.display = 'flex';
}

function clearFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').style.display = 'none';
}

// Drag-over styling
const zone = document.getElementById('uploadZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
zone.addEventListener('drop', () => zone.classList.remove('drag-over'));
</script>
@endsection