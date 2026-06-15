<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('subscription.plans', compact('plans'));
    }

    public function purchase(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:subscription_plans,id']);
        $this->subscriptionService->subscribe(Auth::id(), $request->plan_id);
        return redirect()->route('dashboard')->with('success', 'Langganan berhasil diaktifkan!');
    }
}
