<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class SubscriptionService
{
    public function getActiveSubscription(User $user): ?Subscription
    {
        return Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->first();
    }

    public function subscribe(User $user, string $plan): Subscription
    {
        // Pakej subscription
        $plans = [
            'basic' => ['price' => 99000, 'duration_days' => 30, 'wash_limit' => 4],
            'standard' => ['price' => 179000, 'duration_days' => 30, 'wash_limit' => 8],
            'premium' => ['price' => 299000, 'duration_days' => 30, 'wash_limit' => null], // unlimited
        ];

        if (!isset($plans[$plan])) {
            throw new \InvalidArgumentException("Plan '{$plan}' tidak tersedia.");
        }

        $selected = $plans[$plan];

        return Subscription::create([
            'user_id' => $user->id,
            'plan' => $plan,
            'price' => $selected['price'],
            'wash_limit' => $selected['wash_limit'],
            'wash_used' => 0,
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays($selected['duration_days']),
            'status' => 'active',
        ]);
    }

    public function cancel(Subscription $subscription): Subscription
    {
        $subscription->update(['status' => 'cancelled']);
        return $subscription;
    }

    public function isExpired(Subscription $subscription): bool
    {
        return Carbon::today()->greaterThan($subscription->end_date);
    }

    public function canWash(User $user): bool
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return false;
        }

        if ($this->isExpired($subscription)) {
            $subscription->update(['status' => 'expired']);
            return false;
        }

        // Unlimited (premium)
        if (is_null($subscription->wash_limit)) {
            return true;
        }

        return $subscription->wash_used < $subscription->wash_limit;
    }

    public function useWash(User $user): void
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription || !$this->canWash($user)) {
            throw new \Exception('Tidak ada subscription aktif atau limit sudah habis.');
        }

        $subscription->increment('wash_used');
    }

    public function getRemainingWash(User $user): ?int
    {
        $subscription = $this->getActiveSubscription($user);

        if (!$subscription) {
            return null;
        }

        if (is_null($subscription->wash_limit)) {
            return null; // unlimited
        }

        return max(0, $subscription->wash_limit - $subscription->wash_used);
    }
}
