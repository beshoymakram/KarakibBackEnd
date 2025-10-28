<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    protected $fillable = ['request_id', 'waste_item_id', 'quantity', 'subtotal'];
    protected $casts = [
        'subtotal' => 'decimal:2'
    ];
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function item()
    {
        return $this->belongsTo(WasteItem::class, 'waste_item_id');
    }
}
