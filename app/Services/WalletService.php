<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;

class WalletService
{
    public function getBalance(int $userId): float
    {
        return WalletTransaction::where('user_id', $userId)->sum('amount');
    }

    public function topUp(int $userId, float $amount, string $description = 'Top Up'): WalletTransaction
    {
        return WalletTransaction::create([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description,
        ]);
    }

    public function deduct(int $userId, float $amount, string $description = 'Payment'): ?WalletTransaction
    {
        if ($this->getBalance($userId) < $amount) {
            return null;
        }

        return WalletTransaction::create([
            'user_id' => $userId,
            'amount' => -$amount,
            'type' => 'debit',
            'description' => $description,
        ]);
    }
}
