<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    // GET /admin/subscription-plans
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->get();
        return view('admin.subscription-plans.index', compact('plans'));
    }

    // PUT /admin/subscription-plans/{plan}
    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'stripe_monthly_price_id' => 'nullable|string|max:255',
            'stripe_yearly_price_id'  => 'nullable|string|max:255',
            'stripe_product_id'       => 'nullable|string|max:255',
            'price_monthly'           => 'required|integer|min:0',
            'price_yearly'            => 'required|integer|min:0',
            'platform_fee_pct'        => 'required|numeric|min:0|max:100',
            'job_bids_per_month'      => 'required|integer|min:-1',
            'radius_boost_km'         => 'required|integer|min:0',
        ]);

        $plan->update($validated);

        return back()->with('success', "Plan \"{$plan->name}\" updated successfully.");
    }

    // POST /admin/subscription-plans/{plan}/provision
    public function provision(SubscriptionPlan $plan)
    {
        if ($plan->isFree()) {
            return back()->with('info', 'Basic plan is free — no Stripe price needed.');
        }

        $service = app(SubscriptionService::class);
        $errors  = [];
        $created = [];

        foreach (['monthly', 'yearly'] as $interval) {
            $field    = "stripe_{$interval}_price_id";
            $existing = $plan->$field;

            // Skip if already has a real price ID (not a placeholder)
            if (!empty($existing) && !str_starts_with($existing, 'price_REPLACE')) {
                $created[] = "{$interval}: already set ({$existing})";
                continue;
            }

            try {
                $priceId   = $service->provisionStripePrice($plan, $interval);
                $created[] = "{$interval}: {$priceId}";
            } catch (\Exception $e) {
                $errors[] = "Failed to provision {$interval} price: " . $e->getMessage();
            }
        }

        $plan->refresh();

        if ($errors && empty($created)) {
            return back()->with('error', implode(' | ', $errors));
        }

        $msg = "Stripe prices provisioned for {$plan->name}.";
        if ($plan->stripe_product_id) {
            $msg .= " Product: {$plan->stripe_product_id}.";
        }
        $msg .= ' ' . implode(', ', $created) . '.';

        if ($errors) {
            $msg .= ' Warnings: ' . implode(', ', $errors);
        }

        return back()->with('success', $msg);
    }
}