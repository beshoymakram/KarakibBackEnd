<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends BaseModel
{
    protected $fillable = ['user_id', 'session_id', 'cartable_type', 'cartable_id', 'quantity'];
    protected $appends = ['subtotal', 'points'];

    public function cartable()
    {
        return $this->morphTo();
    }

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
        // Products have prices, waste items have points
        return $this->cartable instanceof Product
            ? $this->cartable->price * $this->quantity
            : 0;
    }

    public function getPointsAttribute()
    {
        return $this->cartable instanceof WasteItem
            ? $this->cartable->points_per_unit * $this->quantity
            : 0;
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
