{{--
    Reusable before/after evidence display.
    Usage: @include('partials.job_evidence', ['record' => $serviceRecord])
--}}
@if(isset($record) && $record && $record->hasEvidence())
<div style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:1.5rem;margin-bottom:1.25rem;position:relative;overflow:hidden;">
    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#6366f1,var(--accent-cyan));"></div>
    <div style="font-family:'Orbitron',sans-serif;font-size:.875rem;font-weight:700;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--border-color);">
        📸 Job Evidence Photos
    </div>

    @if($record->evidence_notes)
    <div style="padding:.875rem 1rem;background:rgba(99,102,241,.06);border:1px solid rgba(99,102,241,.2);border-radius:10px;font-size:.825rem;color:var(--text-secondary);line-height:1.6;margin-bottom:1.25rem;">
        💬 {{ $record->evidence_notes }}
    </div>
    @endif

    <div style="display:grid;grid-template-columns:{{ (!empty($record->before_photos) && !empty($record->after_photos)) ? '1fr 1fr' : '1fr' }};gap:1.25rem;">
        @if(!empty($record->before_photos))
        <div>
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-tertiary);margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;">
                <span style="width:8px;height:8px;border-radius:50%;background:#ff8099;display:inline-block;"></span>
                Before ({{ count($record->before_photos) }})
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:.625rem;">
                @foreach($record->beforePhotoUrls() as $i => $url)
                <div style="position:relative;border-radius:10px;overflow:hidden;aspect-ratio:1;cursor:pointer;background:rgba(0,0,0,.3);"
                     onclick="openEvidenceLightbox('{{ $url }}')">
                    <img src="{{ $url }}" alt="Before photo {{ $i+1 }}"
                         style="width:100%;height:100%;object-fit:cover;transition:transform .3s;"
                         onmouseover="this.style.transform='scale(1.06)'"
                         onmouseout="this.style.transform=''">
                    <div style="position:absolute;bottom:4px;left:5px;font-size:.65rem;color:rgba(255,255,255,.85);font-weight:700;">B{{ $i+1 }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($record->after_photos))
        <div>
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-tertiary);margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--accent-green);display:inline-block;"></span>
                After ({{ count($record->after_photos) }})
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:.625rem;">
                @foreach($record->afterPhotoUrls() as $i => $url)
                <div style="position:relative;border-radius:10px;overflow:hidden;aspect-ratio:1;cursor:pointer;background:rgba(0,0,0,.3);"
                     onclick="openEvidenceLightbox('{{ $url }}')">
                    <img src="{{ $url }}" alt="After photo {{ $i+1 }}"
                         style="width:100%;height:100%;object-fit:cover;transition:transform .3s;"
                         onmouseover="this.style.transform='scale(1.06)'"
                         onmouseout="this.style.transform=''">
                    <div style="position:absolute;bottom:4px;left:5px;font-size:.65rem;color:var(--accent-green);font-weight:700;">A{{ $i+1 }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Lightbox (inline, not via @push so it works everywhere) --}}
@once
<div id="evidenceLightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;align-items:center;justify-content:center;"
     onclick="if(event.target===this)closeEvidenceLightbox()">
    <button onclick="closeEvidenceLightbox()" style="position:absolute;top:1.5rem;right:1.5rem;background:rgba(255,255,255,.1);border:none;border-radius:50%;width:44px;height:44px;color:#fff;font-size:1.25rem;cursor:pointer;">✕</button>
    <img id="evidenceLightboxImg" src="" style="max-width:92vw;max-height:90vh;border-radius:12px;object-fit:contain;box-shadow:0 20px 60px rgba(0,0,0,.5);">
</div>
<script>
function openEvidenceLightbox(src) {
    document.getElementById('evidenceLightboxImg').src = src;
    const lb = document.getElementById('evidenceLightbox');
    lb.style.display = 'flex';
    document.addEventListener('keydown', _escEvidence);
}
function closeEvidenceLightbox() {
    document.getElementById('evidenceLightbox').style.display = 'none';
    document.removeEventListener('keydown', _escEvidence);
}
function _escEvidence(e) { if (e.key === 'Escape') closeEvidenceLightbox(); }
</script>
@endonce
@endif