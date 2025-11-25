<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\OrderObserver;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'grand_total',
        'discount_amount',
        'coupon_code',
        'shipping_address',
    ];

    protected $casts = [
        'shipping_address' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            }
        });
    }

    protected static function booted()
    {
        static::observe(OrderObserver::class);
    }
}
