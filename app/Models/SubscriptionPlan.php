<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'wash_quota', 'duration_days', 'benefits', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'benefits' => 'array',
        'is_active' => 'boolean',
    ];

    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }
}
