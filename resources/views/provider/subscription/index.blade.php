@extends('provider.layouts.app')
@section('title', 'Subscription & Billing')

@section('content')
<style>
.sub-wrap { max-width: 960px; margin: 0 auto; padding: 0 0 4rem; }
.section-title { font-family:'Orbitron',sans-serif; font-size:.75rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--text-tertiary); margin:2.5rem 0 1rem; }

/* Current plan banner */
.current-plan { background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:1.5rem; margin-bottom:2rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; position:relative; overflow:hidden; }
.current-plan::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; }
.plan-basic::before   { background:rgba(255,255,255,.2); }
.plan-pro::before     { background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.plan-premium::before { background:linear-gradient(90deg,#a855f7,#ec4899); }
.plan-name { font-family:'Orbitron',sans-serif; font-size:1.1rem; font-weight:800; }
.plan-name.plan-basic   { color:var(--text-secondary); }
.plan-name.plan-pro     { color:var(--accent-cyan); }
.plan-name.plan-premium { background:linear-gradient(135deg,#a855f7,#ec4899); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
.plan-status { display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .875rem; border-radius:20px; font-size:.75rem; font-weight:700; margin-top:.35rem; }
.status-active   { background:rgba(0,255,170,.12); color:var(--accent-green); border:1px solid rgba(0,255,170,.3); }
.status-pastdue  { background:rgba(255,51,102,.12); color:#ff8099; border:1px solid rgba(255,51,102,.3); }
.status-canceled { background:rgba(255,255,255,.06); color:var(--text-tertiary); border:1px solid rgba(255,255,255,.1); }
.plan-meta { font-size:.8rem; color:var(--text-tertiary); margin-top:.25rem; }

/* Interval toggle */
.interval-toggle { display:inline-flex; background:rgba(0,212,255,.05); border:1px solid var(--border-color); border-radius:10px; padding:3px; margin-bottom:1.5rem; }
.interval-btn { padding:.5rem 1.25rem; border-radius:8px; border:none; background:transparent; color:var(--text-secondary); font-family:'Chakra Petch',sans-serif; font-size:.8rem; font-weight:600; cursor:pointer; transition:all .2s; }
.interval-btn.active { background:var(--accent-cyan); color:#000; }
.save-badge { background:rgba(0,255,170,.15); color:var(--accent-green); border:1px solid rgba(0,255,170,.3); font-size:.68rem; font-weight:700; padding:.15rem .5rem; border-radius:6px; margin-left:.5rem; }

/* Plan cards grid */
.plans-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; }
@media(max-width:768px) { .plans-grid { grid-template-columns:1fr; } }

.plan-card { background:var(--card-bg); border:1px solid var(--border-color); border-radius:18px; padding:1.75rem; display:flex; flex-direction:column; position:relative; overflow:hidden; transition:border-color .3s; }
.plan-card:hover { border-color:rgba(0,212,255,.25); }
.plan-card.is-current { border-color:var(--accent-cyan); }
.plan-card.featured { border-color:rgba(168,85,247,.4); }
.plan-card-top { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.card-basic   .plan-card-top { background:rgba(255,255,255,.15); }
.card-pro     .plan-card-top { background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.card-premium .plan-card-top { background:linear-gradient(90deg,#a855f7,#ec4899); }

.plan-badge { position:absolute; top:1rem; right:1rem; font-size:.68rem; font-weight:700; padding:.2rem .6rem; border-radius:6px; }
.badge-popular  { background:rgba(0,212,255,.15); color:var(--accent-cyan); border:1px solid rgba(0,212,255,.3); }
.badge-best     { background:rgba(168,85,247,.15); color:#c084fc; border:1px solid rgba(168,85,247,.3); }
.badge-current  { background:rgba(0,255,170,.12); color:var(--accent-green); border:1px solid rgba(0,255,170,.3); }

.card-plan-name { font-family:'Orbitron',sans-serif; font-size:.95rem; font-weight:700; margin-bottom:.35rem; }
.card-plan-desc { font-size:.78rem; color:var(--text-tertiary); margin-bottom:1.25rem; line-height:1.6; }
.card-price { margin-bottom:1.25rem; }
.price-amount { font-family:'Orbitron',sans-serif; font-size:2.25rem; font-weight:900; line-height:1; }
.price-monthly-label { font-size:.75rem; color:var(--text-tertiary); margin-left:.25rem; }
.price-yearly-note { font-size:.72rem; color:var(--accent-green); margin-top:.3rem; }

.feature-list { list-style:none; display:flex; flex-direction:column; gap:.6rem; margin-bottom:1.75rem; flex:1; }
.feature-item { display:flex; align-items:flex-start; gap:.625rem; font-size:.8rem; color:var(--text-secondary); }
.feature-item.included .fi-icon { color:var(--accent-green); flex-shrink:0; margin-top:.1rem; }
.feature-item.excluded { opacity:.45; }
.feature-item.excluded .fi-icon { color:var(--text-tertiary); }
.feature-highlight { color:var(--text-primary); font-weight:600; }

.btn-subscribe { width:100%; padding:.875rem; border:none; border-radius:10px; font-family:'Orbitron',sans-serif; font-weight:700; font-size:.78rem; cursor:pointer; transition:all .3s; letter-spacing:.04em; }
.btn-basic   { background:rgba(255,255,255,.06); color:var(--text-secondary); }
.btn-pro     { background:linear-gradient(135deg,var(--accent-cyan),var(--accent-green)); color:#000; }
.btn-pro:hover { transform:translateY(-1px); box-shadow:0 4px 20px rgba(0,212,255,.35); }
.btn-premium { background:linear-gradient(135deg,#a855f7,#ec4899); color:#fff; }
.btn-premium:hover { transform:translateY(-1px); box-shadow:0 4px 20px rgba(168,85,247,.4); }
.btn-current { background:rgba(0,255,170,.08); color:var(--accent-green); border:1px solid rgba(0,255,170,.25); cursor:default; }
.btn-manage  { background:rgba(0,212,255,.08); color:var(--accent-cyan); border:1px solid rgba(0,212,255,.25); }
.btn-manage:hover { background:rgba(0,212,255,.15); }

/* Usage bar */
.usage-bar-wrap { background:var(--card-bg); border:1px solid var(--border-color); border-radius:14px; padding:1.25rem 1.5rem; margin-bottom:1.5rem; }
.usage-label { display:flex; justify-content:space-between; font-size:.8rem; margin-bottom:.625rem; }
.usage-track { height:6px; background:rgba(255,255,255,.08); border-radius:3px; overflow:hidden; }
.usage-fill  { height:100%; border-radius:3px; transition:width .5s ease; }
.usage-ok   { background:linear-gradient(90deg,var(--accent-cyan),var(--accent-green)); }
.usage-warn { background:linear-gradient(90deg,var(--accent-warning),#ff6600); }
.usage-full { background:linear-gradient(90deg,#ff3366,#ff6600); }

/* Invoice table */
.invoice-table { width:100%; border-collapse:collapse; font-size:.82rem; }
.invoice-table th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--text-tertiary); padding:.625rem 1rem; border-bottom:1px solid var(--border-color); text-align:left; }
.invoice-table td { padding:.75rem 1rem; border-bottom:1px solid rgba(0,212,255,.04); color:var(--text-secondary); }
.invoice-table tr:last-child td { border:none; }
.invoice-table tr:hover td { background:rgba(0,212,255,.03); }
.inv-status { display:inline-flex; padding:.15rem .6rem; border-radius:6px; font-size:.7rem; font-weight:700; }
.inv-paid   { background:rgba(0,255,170,.1); color:var(--accent-green); }
.inv-open   { background:rgba(255,170,0,.1); color:var(--accent-warning); }
</style>

<div class="sub-wrap">

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;">{{ session('error') }}</div>
    @endif
    @if(session('info'))
    <div style="background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-cyan);font-size:.875rem;">ℹ {{ session('info') }}</div>
    @endif

    {{-- ── Current Plan Banner ── --}}
    @php
        $currentPlan = $subscription->plan;
        $isActive    = $subscription->isActive();
    @endphp
    <div class="current-plan plan-{{ $currentPlan->slug }}">
        <div>
            <div style="font-size:.72rem;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.35rem;">Current Plan</div>
            <div class="plan-name plan-{{ $currentPlan->slug }}">{{ $currentPlan->name }}</div>
            <div class="plan-status {{ $isActive ? 'status-active' : ($subscription->isPastDue() ? 'status-pastdue' : 'status-canceled') }}">
                {{ $isActive ? '● Active' : ($subscription->isPastDue() ? '⚠ Past Due' : '✕ Canceled') }}
            </div>
            @if($subscription->current_period_end)
            <div class="plan-meta">
                @if($subscription->isCanceled())
                    Access ends {{ $subscription->ends_at?->format('M d, Y') ?? $subscription->current_period_end->format('M d, Y') }}
                @else
                    Renews {{ $subscription->current_period_end->format('M d, Y') }}
                    · {{ $subscription->daysRemaining() }} days remaining
                @endif
            </div>
            @endif
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            @if(!$currentPlan->isFree() && $subscription->stripe_subscription_id)
            <a href="{{ route('provider.subscription.billing-portal') }}" class="btn-subscribe btn-manage" style="width:auto;padding:.65rem 1.25rem;">
                Manage Billing →
            </a>
            @endif
            <div style="text-align:right;">
                <div style="font-family:'Orbitron',sans-serif;font-size:1.5rem;font-weight:900;color:var(--text-primary);">{{ $currentPlan->formattedMonthlyPrice() }}</div>
                @if(!$currentPlan->isFree())<div style="font-size:.72rem;color:var(--text-tertiary);">per month</div>@endif
            </div>
        </div>
    </div>

    {{-- ── Bid Usage Bar (only for limited plans) ── --}}
    @if(!$currentPlan->hasUnlimitedBids())
    @php
        $bidsUsed  = $subscription->bids_used_this_month;
        $bidsTotal = $currentPlan->job_bids_per_month;
        $bidsLeft  = max(0, $bidsTotal - $bidsUsed);
        $pct       = $bidsTotal > 0 ? min(100, round($bidsUsed / $bidsTotal * 100)) : 0;
        $barClass  = $pct >= 90 ? 'usage-full' : ($pct >= 70 ? 'usage-warn' : 'usage-ok');
    @endphp
    <div class="usage-bar-wrap">
        <div class="usage-label">
            <span style="color:var(--text-secondary);">Monthly Bids</span>
            <span style="font-weight:700;color:{{ $pct >= 90 ? '#ff8099' : ($pct >= 70 ? 'var(--accent-warning)' : 'var(--text-primary)') }};">
                {{ $bidsUsed }} / {{ $bidsTotal }} used · <span style="color:var(--accent-cyan);">{{ $bidsLeft }} remaining</span>
            </span>
        </div>
        <div class="usage-track">
            <div class="usage-fill {{ $barClass }}" style="width:{{ $pct }}%;"></div>
        </div>
        @if($bidsLeft <= 2)
        <div style="font-size:.75rem;color:#ff8099;margin-top:.5rem;">
            ⚠ You're almost out of bids this month.
            <a href="#plans" style="color:var(--accent-cyan);text-decoration:underline;">Upgrade for unlimited bids →</a>
        </div>
        @endif
    </div>
    @endif

    {{-- ── Platform Fee Info ── --}}
    <div style="background:rgba(0,212,255,.04);border:1px solid rgba(0,212,255,.1);border-radius:12px;padding:1rem 1.25rem;margin-bottom:2rem;display:flex;align-items:center;gap:.875rem;">
        <span style="font-size:1.25rem;">💰</span>
        <div style="font-size:.8rem;color:var(--text-secondary);">
            Your current platform fee on completed jobs:
            <strong style="font-family:'Orbitron',sans-serif;color:var(--text-primary);margin:0 .25rem;">{{ $currentPlan->platform_fee_pct }}%</strong>
            @if($currentPlan->slug !== 'premium')
            — <a href="#plans" style="color:var(--accent-cyan);text-decoration:underline;">upgrade to reduce it</a>
            @else
            — lowest available rate 🎉
            @endif
        </div>
    </div>

    {{-- ── Plan Cards ── --}}
    <div id="plans">
        <div class="section-title">Choose a Plan</div>

        {{-- Billing interval toggle --}}
        <div class="interval-toggle" id="intervalToggle">
            <button class="interval-btn active" onclick="setInterval('monthly')">Monthly</button>
            <button class="interval-btn" onclick="setInterval('yearly')">Yearly <span class="save-badge">Save 20%</span></button>
        </div>

        <div class="plans-grid">
            @foreach($plans as $plan)
            @php
                $isCurrent = $plan->id === $subscription->plan_id && $isActive;
                $cardClass = "card-{$plan->slug}" . ($plan->slug === 'premium' ? ' featured' : '') . ($isCurrent ? ' is-current' : '');
            @endphp
            <div class="plan-card {{ $cardClass }}">
                <div class="plan-card-top"></div>

                @if($plan->slug === 'pro' && !$isCurrent)
                <span class="plan-badge badge-popular">Most Popular</span>
                @elseif($plan->slug === 'premium' && !$isCurrent)
                <span class="plan-badge badge-best">Best Value</span>
                @elseif($isCurrent)
                <span class="plan-badge badge-current">Current Plan</span>
                @endif

                <div class="card-plan-name" style="{{ $plan->slug === 'premium' ? 'background:linear-gradient(135deg,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;' : ($plan->slug === 'pro' ? 'color:var(--accent-cyan);' : '') }}">
                    {{ $plan->name }}
                </div>
                <div class="card-plan-desc">{{ $plan->description }}</div>

                <div class="card-price">
                    {{-- Monthly price --}}
                    <div class="price-monthly-wrap">
                        <span class="price-amount">{{ $plan->formattedMonthlyPrice() }}</span>
                        @if(!$plan->isFree())<span class="price-monthly-label">/ mo</span>@endif
                    </div>
                    {{-- Yearly equivalent shown when yearly toggle is active --}}
                    @if(!$plan->isFree())
                    <div class="price-yearly-wrap" style="display:none;">
                        <span class="price-amount">{{ $plan->monthlyEquivalentYearly() }}</span>
                        <span class="price-monthly-label">/ mo <span style="color:var(--text-tertiary);">(billed {{ $plan->formattedYearlyPrice() }}/yr)</span></span>
                    </div>
                    <div class="price-yearly-note" style="display:none;">Save {{ $plan->yearlyDiscountPct() }}% vs monthly</div>
                    @endif
                </div>

                <ul class="feature-list">
                    <li class="feature-item included">
                        <svg class="fi-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>
                            @if($plan->hasUnlimitedBids())
                                <span class="feature-highlight">Unlimited</span> job bids
                            @else
                                <span class="feature-highlight">{{ $plan->job_bids_per_month }}</span> bids per month
                            @endif
                        </span>
                    </li>
                    <li class="feature-item included">
                        <svg class="fi-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span><span class="feature-highlight">{{ $plan->platform_fee_pct }}%</span> platform fee on jobs</span>
                    </li>
                    <li class="feature-item {{ $plan->priority_in_job_board ? 'included' : 'excluded' }}">
                        <svg class="fi-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($plan->priority_in_job_board)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            @endif
                        </svg>
                        <span>Priority in job board listings</span>
                    </li>
                    <li class="feature-item {{ $plan->analytics_advanced ? 'included' : 'excluded' }}">
                        <svg class="fi-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($plan->analytics_advanced)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            @endif
                        </svg>
                        <span>Advanced earnings analytics</span>
                    </li>
                    @if($plan->radius_boost_km > 0)
                    <li class="feature-item included">
                        <svg class="fi-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span><span class="feature-highlight">+{{ $plan->radius_boost_km }}km</span> job search radius</span>
                    </li>
                    @endif
                    <li class="feature-item {{ $plan->featured_profile ? 'included' : 'excluded' }}">
                        <svg class="fi-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($plan->featured_profile)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            @endif
                        </svg>
                        <span>Featured profile in directory</span>
                    </li>
                    <li class="feature-item {{ $plan->badge_verified_boost ? 'included' : 'excluded' }}">
                        <svg class="fi-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($plan->badge_verified_boost)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            @endif
                        </svg>
                        <span>Premium verified badge</span>
                    </li>
                </ul>

                {{-- CTA button --}}
                @if($isCurrent)
                <button class="btn-subscribe btn-current" disabled>✓ Current Plan</button>
                @elseif($plan->isFree())
                <button class="btn-subscribe btn-basic" disabled>Free Forever</button>
                @else
                <form method="POST" action="{{ route('provider.subscription.checkout') }}" class="checkout-form">
                    @csrf
                    <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
                    <input type="hidden" name="interval" value="monthly" class="interval-input">
                    <button type="submit" class="btn-subscribe btn-{{ $plan->slug }}">
                        @if($subscription->plan->sort_order < $plan->sort_order)
                            Upgrade to {{ $plan->name }} →
                        @else
                            Switch to {{ $plan->name }}
                        @endif
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Billing History ── --}}
    @if($invoices->isNotEmpty())
    <div class="section-title" style="margin-top:3rem;">Billing History</div>
    <div style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;overflow:hidden;">
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Plan</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                <tr>
                    <td>{{ $inv->paid_at?->format('M d, Y') ?? '—' }}</td>
                    <td>{{ $inv->plan->name }}</td>
                    <td style="font-family:'Orbitron',sans-serif;font-weight:700;color:var(--text-primary);">{{ $inv->formattedAmount() }}</td>
                    <td><span class="inv-status inv-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span></td>
                    <td>
                        @if($inv->hosted_invoice_url)
                        <a href="{{ $inv->hosted_invoice_url }}" target="_blank" style="color:var(--accent-cyan);text-decoration:none;font-size:.78rem;">PDF ↗</a>
                        @else
                        <span style="color:var(--text-tertiary);">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

<script>
var currentInterval = 'monthly';

function setInterval(val) {
    currentInterval = val;

    // Toggle button states
    document.querySelectorAll('.interval-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.textContent.toLowerCase().includes(val));
    });

    // Toggle price displays
    var showMonthly = val === 'monthly';
    document.querySelectorAll('.price-monthly-wrap').forEach(function(el) { el.style.display = showMonthly ? '' : 'none'; });
    document.querySelectorAll('.price-yearly-wrap').forEach(function(el)  { el.style.display = showMonthly ? 'none' : ''; });
    document.querySelectorAll('.price-yearly-note').forEach(function(el)  { el.style.display = showMonthly ? 'none' : ''; });

    // Update hidden interval inputs in checkout forms
    document.querySelectorAll('.interval-input').forEach(function(input) {
        input.value = val;
    });
}
</script>
@endsection