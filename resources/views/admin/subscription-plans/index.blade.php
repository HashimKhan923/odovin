@extends('admin.layouts.app')
@section('title', 'Manage Subscription Plans')

@section('content')
<style>
.admin-wrap { max-width: 860px; margin: 0 auto; }
.plan-admin-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; margin-bottom: 1.5rem; overflow: hidden; }
.plan-admin-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.plan-admin-title { font-family: 'Orbitron', sans-serif; font-size: 1rem; font-weight: 700; }
.plan-admin-body { padding: 1.5rem; }
.field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
@media(max-width: 600px) { .field-grid { grid-template-columns: 1fr; } }
.field-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text-tertiary); display: block; margin-bottom: .35rem; }
.field-input { width: 100%; padding: .65rem 1rem; background: rgba(0,212,255,.04); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: 'Chakra Petch', sans-serif; font-size: .875rem; transition: border-color .2s; }
.field-input:focus { outline: none; border-color: var(--accent-cyan); }
.price-id-wrap { position: relative; }
.price-id-status { position: absolute; right: .75rem; top: 50%; transform: translateY(-50%); font-size: .72rem; font-weight: 700; pointer-events: none; }
.price-ok   { color: var(--accent-green); }
.price-bad  { color: #ff8099; }
.btn-save { padding: .6rem 1.25rem; background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green)); border: none; border-radius: 8px; color: #000; font-family: 'Orbitron', sans-serif; font-weight: 700; font-size: .72rem; cursor: pointer; transition: all .25s; }
.btn-save:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(0,212,255,.3); }
.btn-provision { padding: .6rem 1.25rem; background: rgba(168,85,247,.12); border: 1px solid rgba(168,85,247,.3); border-radius: 8px; color: #c084fc; font-family: 'Orbitron', sans-serif; font-weight: 700; font-size: .72rem; cursor: pointer; transition: all .25s; }
.btn-provision:hover { background: rgba(168,85,247,.22); }
.hint { font-size: .72rem; color: var(--text-tertiary); margin-top: .3rem; }
.product-id-row { background: rgba(0,255,170,.04); border: 1px solid rgba(0,255,170,.15); border-radius: 8px; padding: .625rem 1rem; margin-bottom: 1rem; font-size: .78rem; color: var(--text-secondary); display: flex; align-items: center; gap: .5rem; }
</style>

<div class="admin-wrap">
    <div style="margin-bottom:1.5rem;">
        <h1 style="font-family:'Orbitron',sans-serif;font-size:1.25rem;font-weight:800;margin-bottom:.35rem;">Subscription Plans</h1>
        <p style="font-size:.825rem;color:var(--text-tertiary);">
            Click <strong>⚡ Auto-Provision</strong> to create Stripe products and prices automatically.
            Or paste existing price IDs from your
            <a href="https://dashboard.stripe.com/products" target="_blank" style="color:var(--accent-cyan);">Stripe Dashboard ↗</a>
        </p>
    </div>

    @if(session('success'))
    <div style="background:rgba(0,255,170,.1);border:1px solid rgba(0,255,170,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-green);font-size:.875rem;word-break:break-all;">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:#ff8099;font-size:.875rem;word-break:break-all;">
        {{ session('error') }}
    </div>
    @endif
    @if(session('info'))
    <div style="background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.3);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;color:var(--accent-cyan);font-size:.875rem;">
        ℹ {{ session('info') }}
    </div>
    @endif

    @foreach($plans as $plan)
    @php
        $monthlyOk  = !empty($plan->stripe_monthly_price_id) && !str_starts_with($plan->stripe_monthly_price_id, 'price_REPLACE');
        $yearlyOk   = !empty($plan->stripe_yearly_price_id)  && !str_starts_with($plan->stripe_yearly_price_id, 'price_REPLACE');
        $productSet = !empty($plan->stripe_product_id);
        $fullyReady = $plan->isFree() || ($monthlyOk && $yearlyOk);
    @endphp
    <div class="plan-admin-card">
        <div class="plan-admin-header">
            <div>
                <div class="plan-admin-title"
                    style="{{ $plan->slug === 'pro' ? 'color:var(--accent-cyan);' : ($plan->slug === 'premium' ? 'background:linear-gradient(135deg,#a855f7,#ec4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;' : 'color:var(--text-secondary);') }}">
                    {{ $plan->name }}
                    @if($fullyReady)
                        <span style="font-size:.65rem;background:rgba(0,255,170,.12);color:var(--accent-green);border:1px solid rgba(0,255,170,.25);border-radius:6px;padding:.1rem .5rem;margin-left:.5rem;font-family:'Chakra Petch',sans-serif;vertical-align:middle;">✓ Ready</span>
                    @elseif(!$plan->isFree())
                        <span style="font-size:.65rem;background:rgba(255,170,0,.12);color:var(--accent-warning);border:1px solid rgba(255,170,0,.25);border-radius:6px;padding:.1rem .5rem;margin-left:.5rem;font-family:'Chakra Petch',sans-serif;vertical-align:middle;">⚠ Needs Setup</span>
                    @endif
                </div>
                <div style="font-size:.75rem;color:var(--text-tertiary);margin-top:.25rem;">
                    {{ $plan->formattedMonthlyPrice() }}/mo · {{ $plan->platform_fee_pct }}% platform fee
                </div>
            </div>
            @if(!$plan->isFree())
            <form method="POST" action="{{ route('admin.subscription-plans.provision', $plan) }}">
                @csrf
                <button type="submit" class="btn-provision"
                    onclick="return confirm('Auto-create Stripe products and prices for {{ $plan->name }}? Existing valid price IDs will be kept.')">
                    ⚡ Auto-Provision in Stripe
                </button>
            </form>
            @else
            <span style="font-size:.75rem;color:var(--text-tertiary);padding:.5rem 1rem;background:rgba(255,255,255,.04);border-radius:8px;">Free — no Stripe setup needed</span>
            @endif
        </div>

        <div class="plan-admin-body">

            {{-- Show existing product ID if set --}}
            @if($productSet)
            <div class="product-id-row">
                <span style="color:var(--accent-green);">✓</span>
                <span>Stripe Product: <strong style="font-family:'Chakra Petch',sans-serif;color:var(--text-primary);">{{ $plan->stripe_product_id }}</strong></span>
                <a href="https://dashboard.stripe.com/products/{{ $plan->stripe_product_id }}" target="_blank" style="color:var(--accent-cyan);font-size:.72rem;margin-left:.25rem;">View ↗</a>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.subscription-plans.update', $plan) }}">
                @csrf @method('PUT')

                {{-- Stripe IDs --}}
                @if(!$plan->isFree())
                <div style="margin-bottom:1.25rem;">
                    <div class="field-grid">
                        <div>
                            <label class="field-label">Stripe Product ID</label>
                            <div class="price-id-wrap">
                                <input type="text" name="stripe_product_id" class="field-input"
                                    value="{{ $plan->stripe_product_id }}"
                                    placeholder="prod_xxxxxxxxxxxx">
                                <span class="price-id-status {{ $productSet ? 'price-ok' : 'price-bad' }}">
                                    {{ $productSet ? '✓' : '✗' }}
                                </span>
                            </div>
                            <p class="hint">Auto-filled by provisioning. Or copy from Stripe Dashboard.</p>
                        </div>
                        <div>{{-- spacer --}}</div>
                        <div>
                            <label class="field-label">Stripe Monthly Price ID</label>
                            <div class="price-id-wrap">
                                <input type="text" name="stripe_monthly_price_id" class="field-input"
                                    style="padding-right:4rem;"
                                    value="{{ $plan->stripe_monthly_price_id }}"
                                    placeholder="price_xxxxxxxxxxxx">
                                <span class="price-id-status {{ $monthlyOk ? 'price-ok' : 'price-bad' }}">
                                    {{ $monthlyOk ? '✓ Set' : '✗ Missing' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="field-label">Stripe Yearly Price ID</label>
                            <div class="price-id-wrap">
                                <input type="text" name="stripe_yearly_price_id" class="field-input"
                                    style="padding-right:4rem;"
                                    value="{{ $plan->stripe_yearly_price_id }}"
                                    placeholder="price_xxxxxxxxxxxx">
                                <span class="price-id-status {{ $yearlyOk ? 'price-ok' : 'price-bad' }}">
                                    {{ $yearlyOk ? '✓ Set' : '✗ Missing' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                    <input type="hidden" name="stripe_product_id" value="">
                    <input type="hidden" name="stripe_monthly_price_id" value="">
                    <input type="hidden" name="stripe_yearly_price_id" value="">
                @endif

                {{-- Pricing & limits --}}
                <div class="field-grid">
                    <div>
                        <label class="field-label">Monthly Price (cents)</label>
                        <input type="number" name="price_monthly" class="field-input" value="{{ $plan->price_monthly }}" min="0">
                        <p class="hint">e.g. 2999 = $29.99. 0 = free.</p>
                    </div>
                    <div>
                        <label class="field-label">Yearly Price (cents)</label>
                        <input type="number" name="price_yearly" class="field-input" value="{{ $plan->price_yearly }}" min="0">
                        <p class="hint">e.g. 28799 = $287.99/yr</p>
                    </div>
                    <div>
                        <label class="field-label">Platform Fee %</label>
                        <input type="number" name="platform_fee_pct" class="field-input" value="{{ $plan->platform_fee_pct }}" min="0" max="100" step="0.01">
                    </div>
                    <div>
                        <label class="field-label">Bids / Month (-1 = unlimited)</label>
                        <input type="number" name="job_bids_per_month" class="field-input" value="{{ $plan->job_bids_per_month }}" min="-1">
                    </div>
                    <div>
                        <label class="field-label">Radius Boost (km)</label>
                        <input type="number" name="radius_boost_km" class="field-input" value="{{ $plan->radius_boost_km }}" min="0">
                    </div>
                </div>

                <div style="margin-top:1.25rem;display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection