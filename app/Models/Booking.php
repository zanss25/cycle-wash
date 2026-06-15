<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'service_id', 'queue_number',
        'vehicle_type', 'plate_number',
        'booking_date', 'booking_time', 'status',
        'payment_method', 'payment_status',
        'total_price', 'notes',
        'started_at', 'completed_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_price' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function scopeToday($query)
    {
        return $query->where('booking_date', now()->toDateString());
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['antrian', 'dicuci']);
    }
}
