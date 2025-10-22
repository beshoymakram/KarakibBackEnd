<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends BaseModel
{
    protected $fillable = ['user_id', 'session_id', 'product_id', 'quantity'];
    protected $appends = ['subtotal'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->product->price;
    }

    // Scope for getting cart by user or session
    public function scopeForCart($query, $userId = null, $sessionId = null)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }

        if ($sessionId) {
            return $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        return $query->whereRaw('1 = 0'); // Empty result
    }
}
