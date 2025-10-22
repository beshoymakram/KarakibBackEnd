<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'user_address_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'total',
        'status',
        'address',
        'phone',
        'payment_method'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateNumber()
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
