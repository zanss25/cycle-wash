<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;

class PaymentService
{
    public function deductWallet(User $user, float $amount, string $description): bool
    {
        if ($user->wallet_balance < $amount) {
            return false;
        }

        $user->decrement('wallet_balance', $amount);

        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'payment',
            'amount' => -$amount,
            'balance_after' => $user->fresh()->wallet_balance,
            'description' => $description,
        ]);

        return true;
    }

    public function topupWallet(User $user, float $amount, string $method): WalletTransaction
    {
        $user->increment('wallet_balance', $amount);

        return WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount' => $amount,
            'balance_after' => $user->fresh()->wallet_balance,
            'description' => 'Top up via ' . strtoupper($method),
        ]);
    }

    public function refundWallet(User $user, float $amount, string $description): WalletTransaction
    {
        $user->increment('wallet_balance', $amount);

        return WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'refund',
            'amount' => $amount,
            'balance_after' => $user->fresh()->wallet_balance,
            'description' => $description,
        ]);
    }
}
