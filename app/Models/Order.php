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
        'courier_id',
        'collected_at',
        'status',
        'qr_code',
        'is_paid',
        'payment_method'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'collected_at' => 'datetime',
        'is_paid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id')->withTrashed();
    }

    public function assignCourier($courierId)
    {
        $this->update([
            'courier_id' => $courierId,
            'status' => 'assigned',
        ]);
    }

    public function unassignCourier()
    {
        if ($this->status !== 'paid') {
            $this->update([
                'courier_id' => null,
                'status' => 'pending',
            ]);
        } else {
            $this->update([
                'courier_id' => null,
                'status' => 'paid',
            ]);
        }
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id')->withTrashed();
    }

    public static function generateNumber()
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
