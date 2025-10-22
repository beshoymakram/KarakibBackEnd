<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends BaseModel
{
    protected $fillable = [
        'order_number',
        'user_id',
        'user_address_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'total',
        'status',
        'payment_method'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
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
