<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return response()->json($plans);
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|in:qris,gopay,ovo,dana,wallet',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $user = auth()->user();

        // Check existing active subscription
        if ($user->activeSubscription) {
            return response()->json(['error' => 'Anda masih memiliki langganan aktif'], 422);
        }

        // Process payment
        if ($request->payment_method === 'wallet') {
            if ($user->wallet_balance < $plan->price) {
                return response()->json(['error' => 'Saldo wallet tidak mencukupi'], 422);
            }
            $user->decrement('wallet_balance', $plan->price);
        }

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'remaining_quota' => $plan->wash_quota,
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
            'status' => 'active',
            'auto_renew' => $request->get('auto_renew', false),
        ]);

        Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'method' => $request->payment_method,
            'status' => 'success',
            'transaction_ref' => 'SUB-' . strtoupper(uniqid()),
            'paid_at' => now(),
        ]);

        return response()->json([
            'message' => 'Langganan berhasil diaktifkan!',
            'subscription' => $subscription->load('plan'),
        ], 201);
    }

    public function mySubscription()
    {
        $subscription = auth()->user()->activeSubscription;

        if (!$subscription) {
            return response()->json(['message' => 'Tidak ada langganan aktif'], 404);
        }

        return response()->json($subscription->load('plan'));
    }

    public function renew(UserSubscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $plan = $subscription->plan;

        $subscription->update([
            'remaining_quota' => $plan->wash_quota,
            'starts_at' => now(),
            'expires_at' => now()->addDays($plan->duration_days),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Langganan berhasil diperpanjang!',
            'subscription' => $subscription->fresh()->load('plan'),
        ]);
    }
}
