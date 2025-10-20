<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
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
