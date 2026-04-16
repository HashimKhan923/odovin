@extends('provider.layouts.app')
@section('title', 'Payouts')

@section('content')
<style>
.pay-wrap   { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem; }
.section-title {
    font-family: 'Orbitron', sans-serif; font-size: .85rem; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--text-tertiary); margin: 2rem 0 .875rem;
}
.section-title:first-of-type { margin-top: 0; }

/* ── Stats row ── */
.stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-card {
    background: var(--card-bg); border: 1px solid var(--border-color);
    border-radius: 14px; padding: 1.25rem 1.5rem;
    position: relative; overflow: hidden;
}
.stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
}
.stat-card.green::before  { background: linear-gradient(90deg, var(--accent-green), transparent); }
.stat-card.cyan::before   { background: linear-gradient(90deg, var(--accent-cyan), transparent); }
.stat-card.purple::before { background: linear-gradient(90deg, var(--accent-purple), transparent); }
.stat-label { font-size: .72rem; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: .07em; margin-bottom: .5rem; }
.stat-value { font-family: 'Orbitron', sans-serif; font-size: 1.5rem; font-weight: 800; }
.stat-value.green  { color: var(--accent-green); }
.stat-value.cyan   { color: var(--accent-cyan); }
.stat-value.purple { color: var(--accent-purple); }

/* ── Account card ── */
.account-card {
    background: var(--card-bg); border: 1px solid var(--border-color);
    border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem;
    position: relative; overflow: hidden;
}
.account-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, var(--accent-cyan), var(--accent-green));
}
.account-card-head {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;
}
.account-card-title {
    font-family: 'Orbitron', sans-serif; font-size: .95rem; font-weight: 700;
}
.status-badge {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .35rem .875rem; border-radius: 20px; font-size: .78rem; font-weight: 700;
}
.badge-ok   { background: rgba(0,255,170,.12); color: var(--accent-green); border: 1px solid rgba(0,255,170,.3); }
.badge-warn { background: rgba(255,170,0,.12); color: var(--accent-warning); border: 1px solid rgba(255,170,0,.3); }
.info-row { display: flex; justify-content: space-between; align-items: center; font-size: .85rem; padding: .5rem 0; border-bottom: 1px solid var(--border-color); }
.info-row:last-child { border: none; padding-bottom: 0; }
.info-row .k { color: var(--text-tertiary); }
.info-row .v { color: var(--text-primary); font-weight: 600; font-family: 'Chakra Petch', sans-serif; }
.btn-onboard {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .7rem 1.5rem; background: linear-gradient(135deg, #6772e5, #9b59b6);
    border: none; border-radius: 10px; color: #fff;
    font-family: 'Orbitron', sans-serif; font-weight: 700; font-size: .78rem;
    text-decoration: none; transition: all .3s; cursor: pointer;
}
.btn-onboard:hover { transform: translateY(-1px); box-shadow: 0 4px 20px rgba(103,114,229,.4); }

/* ── Transaction table ── */
.tx-card {
    background: var(--card-bg); border: 1px solid var(--border-color);
    border-radius: 16px; overflow: hidden; margin-bottom: 1.5rem;
}
.tx-head {
    display: grid; grid-template-columns: 1fr 1.5fr 1fr 1fr 1fr;
    padding: .75rem 1.25rem;
    background: rgba(0,212,255,.04);
    border-bottom: 1px solid var(--border-color);
    font-size: .72rem; font-weight: 700; color: var(--text-tertiary);
    text-transform: uppercase; letter-spacing: .07em;
}
.tx-row {
    display: grid; grid-template-columns: 1fr 1.5fr 1fr 1fr 1fr;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border-color);
    font-size: .85rem; align-items: center;
    transition: background .2s;
}
.tx-row:last-child { border: none; }
.tx-row:hover { background: rgba(0,212,255,.03); }
.tx-job  { font-family: 'Chakra Petch', sans-serif; color: var(--accent-cyan); font-weight: 600; font-size: .8rem; }
.tx-svc  { color: var(--text-primary); }
.tx-date { color: var(--text-tertiary); font-size: .78rem; }
.tx-fee  { color: var(--text-tertiary); font-size: .78rem; }
.tx-amt  { font-family: 'Orbitron', sans-serif; font-weight: 800; font-size: .95rem; }
.tx-amt.released { color: var(--accent-green); }
.tx-amt.pending  { color: var(--accent-warning); }
.pill-released { background: rgba(0,255,170,.1); color: var(--accent-green); border: 1px solid rgba(0,255,170,.2); padding: .2rem .6rem; border-radius: 8px; font-size: .7rem; font-weight: 700; }
.pill-pending  { background: rgba(255,170,0,.1);  color: var(--accent-warning); border: 1px solid rgba(255,170,0,.2); padding: .2rem .6rem; border-radius: 8px; font-size: .7rem; font-weight: 700; }
.empty-state { text-align: center; padding: 2.5rem; color: var(--text-tertiary); font-size: .875rem; }
.empty-icon  { font-size: 2rem; margin-bottom: .75rem; }

/* test mode notice */
.test-notice {
    background: rgba(255,170,0,.06); border: 1px solid rgba(255,170,0,.2);
    border-radius: 10px; padding: .875rem 1.1rem; margin-bottom: 1.5rem;
    font-size: .8rem; color: var(--accent-warning); display: flex; align-items: flex-start; gap: .625rem;
    line-height: 1.6;
}

@media(max-width:700px) {
    .tx-head, .tx-row { grid-template-columns: 1fr 1fr 1fr; }
    .tx-head > *:nth-child(2),
    .tx-row  > *:nth-child(2),
    .tx-head > *:nth-child(4),
    .tx-row  > *:nth-child(4) { display: none; }
}
</style>

<div class="pay-wrap">

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

    {{-- Test mode notice --}}
    @if(config('app.env') !== 'production')
    <div class="test-notice">
        <span>⚠</span>
        <span>
            <strong>Test mode active.</strong> Transfers shown here are Stripe test transactions —
            no real money moves. Amounts reflect what would be sent to your connected bank account in production.
            Check your <a href="https://dashboard.stripe.com/test/transfers" target="_blank" style="color:var(--accent-warning);">Stripe test dashboard</a> to see the transfer records.
        </span>
    </div>
    @endif

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card green">
            <div class="stat-label">Total Earned</div>
            <div class="stat-value green">${{ number_format($totalEarned / 100, 2) }}</div>
        </div>
        <div class="stat-card cyan">
            <div class="stat-label">Pending Release</div>
            <div class="stat-value cyan">${{ number_format($totalPending / 100, 2) }}</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Completed Jobs</div>
            <div class="stat-value purple">{{ $transactions->count() }}</div>
        </div>
    </div>

    {{-- Stripe account status --}}
    <div class="section-title">Stripe Account</div>
    <div class="account-card">
        <div class="account-card-head">
            <div class="account-card-title">💳 Payout Setup</div>
            @if($provider->payout_enabled)
                <span class="status-badge badge-ok">✓ Payouts Enabled</span>
            @else
                <span class="status-badge badge-warn">⚠ Setup Required</span>
            @endif
        </div>

        @if($provider->payout_enabled)
            <div class="info-row">
                <span class="k">Stripe Account ID</span>
                <span class="v" style="font-size:.78rem;">{{ $provider->stripe_account_id }}</span>
            </div>
            <div class="info-row">
                <span class="k">Onboarded</span>
                <span class="v">{{ $provider->stripe_onboarded_at?->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="k">Status</span>
                <span class="v" style="color:var(--accent-green);">Active — Ready to receive payments</span>
            </div>
            <div class="info-row">
                <span class="k">Payout schedule</span>
                <span class="v">1–2 business days after job confirmation</span>
            </div>
        @else
            <p style="font-size:.875rem;color:var(--text-secondary);margin-bottom:1.5rem;line-height:1.7;">
                Connect your Stripe account to receive payments from consumers.
                Takes about 5 minutes — you'll need your bank details and ID.
            </p>
            <a href="{{ route('provider.payments.onboard') }}" class="btn-onboard">
                Connect Stripe Account →
            </a>
            <p style="font-size:.78rem;color:var(--text-tertiary);margin-top:.875rem;">
                Odovin never stores your bank details. All payouts are managed by Stripe.
            </p>
        @endif
    </div>

    {{-- Pending (held) payments --}}
    @if($pending->isNotEmpty())
    <div class="section-title">Pending Release ({{ $pending->count() }})</div>
    <div class="tx-card">
        <div class="tx-head">
            <div>Job</div>
            <div>Service</div>
            <div>Held Since</div>
            <div>Platform Fee</div>
            <div>You Receive</div>
        </div>
        @foreach($pending as $tx)
        <div class="tx-row">
            <div class="tx-job">#{{ $tx->jobPost->job_number }}</div>
            <div class="tx-svc">{{ $tx->jobPost->service_type }}</div>
            <div class="tx-date">{{ $tx->held_at?->format('M d, Y') }}</div>
            <div class="tx-fee">-${{ number_format($tx->platform_fee / 100, 2) }}</div>
            <div>
                <div class="tx-amt pending">${{ number_format($tx->providerAmount() / 100, 2) }}</div>
                <span class="pill-pending">Awaiting Release</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Released payments history --}}
    <div class="section-title">Payment History</div>
    @if($transactions->isEmpty())
    <div class="tx-card">
        <div class="empty-state">
            <div class="empty-icon">💸</div>
            <p>No released payments yet.</p>
            <p style="margin-top:.5rem;font-size:.8rem;">Completed jobs where consumers have confirmed payment will appear here.</p>
        </div>
    </div>
    @else
    <div class="tx-card">
        <div class="tx-head">
            <div>Job</div>
            <div>Service</div>
            <div>Released</div>
            <div>Platform Fee</div>
            <div>You Received</div>
        </div>
        @foreach($transactions as $tx)
        <div class="tx-row">
            <div class="tx-job">#{{ $tx->jobPost->job_number }}</div>
            <div class="tx-svc">{{ $tx->jobPost->service_type }}</div>
            <div class="tx-date">{{ $tx->released_at?->format('M d, Y') }}</div>
            <div class="tx-fee">-${{ number_format($tx->platform_fee / 100, 2) }}</div>
            <div>
                <div class="tx-amt released">${{ number_format($tx->providerAmount() / 100, 2) }}</div>
                <span class="pill-released">Released</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection