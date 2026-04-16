@extends('layouts.app')
@section('title', 'Complete Payment — ' . $job->job_number)

@section('content')
<style>
.pay-pg {
    max-width: 560px;
    margin: 2.5rem auto;
    padding: 0 1.25rem;
    font-family: 'Chakra Petch', sans-serif;
}
.pay-back {
    display: inline-flex; align-items: center; gap: .5rem;
    color: var(--accent-cyan); text-decoration: none;
    font-size: .875rem; font-weight: 600; margin-bottom: 1.5rem;
    transition: gap .2s;
}
.pay-back:hover { gap: .875rem; }
.pay-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1.75rem;
    margin-bottom: 1.25rem;
    position: relative;
    overflow: hidden;
}
.pay-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, var(--accent-cyan), var(--accent-green));
}
.pay-title {
    font-family: 'Orbitron', sans-serif;
    font-size: .95rem;
    font-weight: 700;
    color: var(--accent-cyan);
    margin-bottom: 1.25rem;
    padding-bottom: .875rem;
    border-bottom: 1px solid var(--border-color);
}
.sum-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .875rem;
    padding: .5rem 0;
    border-bottom: 1px solid var(--border-color);
}
.sum-row:last-child { border: none; }
.sum-row .lbl { color: var(--text-tertiary); }
.sum-row .val { color: var(--text-primary); font-weight: 600; }
.sum-row.total .lbl { color: var(--text-secondary); font-weight: 700; font-size: .95rem; }
.sum-row.total .val {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--accent-warning);
}
#stripe-el {
    background: rgba(0,212,255,.04);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 14px 16px;
    margin: 1.25rem 0 .5rem;
    transition: border-color .3s;
    min-height: 42px;
}
#stripe-el.StripeElement--focus {
    border-color: var(--accent-cyan);
    box-shadow: 0 0 0 3px rgba(0,212,255,.1);
}
#stripe-el.StripeElement--invalid { border-color: #ff3366; }
#card-errors {
    color: #ff6b6b;
    font-size: .8rem;
    margin-bottom: .875rem;
    min-height: 20px;
}
.btn-pay {
    width: 100%;
    padding: .9rem;
    background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
    border: none;
    border-radius: 10px;
    color: #000;
    font-family: 'Orbitron', sans-serif;
    font-weight: 800;
    font-size: .85rem;
    cursor: pointer;
    transition: all .3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .6rem;
    letter-spacing: .04em;
}
.btn-pay:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(0,212,255,.4);
}
.btn-pay:disabled { opacity: .55; cursor: not-allowed; transform: none; }
.escrow-note {
    font-size: .78rem;
    color: var(--text-tertiary);
    text-align: center;
    margin-top: 1rem;
    line-height: 1.7;
    padding: .875rem;
    background: rgba(0,212,255,.03);
    border: 1px solid rgba(0,212,255,.1);
    border-radius: 8px;
}
.escrow-note strong { color: var(--text-secondary); }
.stripe-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    font-size: .72rem;
    color: var(--text-tertiary);
    margin-top: .875rem;
}
.spinner-sm {
    width: 16px; height: 16px;
    border: 2px solid rgba(0,0,0,.3);
    border-top-color: #000;
    border-radius: 50%;
    animation: paySpin .7s linear infinite;
    flex-shrink: 0;
}
@keyframes paySpin { to { transform: rotate(360deg); } }
</style>

<div class="pay-pg">

    <a href="{{ route('jobs.show', $job) }}" class="pay-back">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Job
    </a>

    {{-- Order summary --}}
    <div class="pay-card">
        <div class="pay-title">🔒 Secure Escrow Payment</div>
        <div class="sum-row">
            <span class="lbl">Job number</span>
            <span class="val">#{{ $job->job_number }}</span>
        </div>
        <div class="sum-row">
            <span class="lbl">Service</span>
            <span class="val">{{ $job->service_type }}</span>
        </div>
        <div class="sum-row">
            <span class="lbl">Vehicle</span>
            <span class="val">{{ $job->vehicle->year }} {{ $job->vehicle->make }} {{ $job->vehicle->model }}</span>
        </div>
        <div class="sum-row">
            <span class="lbl">Provider</span>
            <span class="val">{{ $acceptedOffer->serviceProvider->business_name ?? $acceptedOffer->serviceProvider->name }}</span>
        </div>
        <div class="sum-row total">
            <span class="lbl">Total</span>
            <span class="val">${{ number_format($acceptedOffer->offered_price, 2) }}</span>
        </div>
    </div>

    {{-- Card form --}}
    <div class="pay-card">
        <div class="pay-title">💳 Card Details</div>
        <div id="stripe-el"></div>
        <div id="card-errors" role="alert"></div>
        <button id="pay-btn" class="btn-pay">
            ⚡ <span id="pay-btn-text">Pay ${{ number_format($acceptedOffer->offered_price, 2) }} — Held in Escrow</span>
        </button>
        <div class="escrow-note">
            Your payment is held <strong>securely in escrow</strong>. Funds are only released
            to the provider after you confirm the work is done — or automatically after 72 hours.
        </div>
        <div class="stripe-badge">
            <svg width="44" height="18" viewBox="0 0 60 24" xmlns="http://www.w3.org/2000/svg">
                <text x="0" y="18" font-size="16" fill="#6772e5" font-family="Arial, sans-serif" font-weight="bold">stripe</text>
            </svg>
            Payments secured by Stripe
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
(async function () {

    const STRIPE_PK = '{{ config("services.stripe.key") }}';
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
    const errEl     = document.getElementById('card-errors');
    const btn       = document.getElementById('pay-btn');
    const SYNC_URL  = '{{ route("jobs.payment.sync", $job) }}';
    const RETURN_URL = '{{ route("jobs.show", $job) }}';

    if (!STRIPE_PK || !STRIPE_PK.startsWith('pk_')) {
        errEl.textContent = '⚠ Stripe is not configured. Set STRIPE_KEY=pk_test_... in your .env file.';
        btn.disabled = true;
        return;
    }

    const stripe   = Stripe(STRIPE_PK);
    const elements = stripe.elements();
    const card     = elements.create('card', {
        style: {
            base: {
                color: '#e0e0e0',
                fontFamily: '"Chakra Petch", "Helvetica Neue", sans-serif',
                fontSize: '15px',
                fontSmoothing: 'antialiased',
                '::placeholder': { color: '#556070' },
                iconColor: '#00d4ff',
            },
            invalid: { color: '#ff6b6b', iconColor: '#ff6b6b' },
        },
        hidePostalCode: true,
    });
    card.mount('#stripe-el');
    card.on('change', e => { errEl.textContent = e.error ? '⚠ ' + e.error.message : ''; });

    // Fetch PaymentIntent
    let clientSecret = null;
    try {
        const res  = await fetch('{{ route("jobs.payment.intent", $job) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.error) { errEl.textContent = '⚠ ' + data.error; btn.disabled = true; return; }
        clientSecret = data.client_secret;
    } catch (err) {
        errEl.textContent = '⚠ Could not initialise payment. Please refresh and try again.';
        btn.disabled = true;
        return;
    }

    // Pay button handler
    btn.addEventListener('click', async function () {
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner-sm"></div><span>Processing…</span>';

        const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: { card }
        });

        if (error) {
            errEl.textContent = '⚠ ' + error.message;
            btn.disabled = false;
            btn.innerHTML = '⚡ <span>Pay ${{ number_format($acceptedOffer->offered_price, 2) }} — Held in Escrow</span>';
            return;
        }

        if (paymentIntent.status === 'succeeded') {
            btn.innerHTML = '✓ <span>Payment Confirmed — Updating booking…</span>';
            btn.style.background = 'linear-gradient(135deg,#00ffaa,#00d4ff)';

            // ── KEY FIX: sync DB immediately from Stripe, don't wait for webhook ──
            try {
                await fetch(SYNC_URL, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
                });
            } catch (e) {
                // Sync failed — webhook will catch it. Still redirect.
            }

            btn.innerHTML = '✓ <span>Booking Confirmed — Redirecting…</span>';
            window.location.href = RETURN_URL;
        }
    });

})();
</script>
@endpush