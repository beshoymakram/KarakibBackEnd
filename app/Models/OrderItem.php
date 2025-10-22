<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends BaseModel
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];
    protected $casts = [
        'price' => 'decimal:2'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
