<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'plan_id', 'remaining_quota', 'starts_at', 'expires_at',
        'status', 'auto_renew',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'expires_at' => 'date',
        'auto_renew' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->expires_at >= now();
    }

    public function hasQuota()
    {
        return $this->remaining_quota > 0;
    }
}
