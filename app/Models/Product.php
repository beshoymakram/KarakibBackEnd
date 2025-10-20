<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'category_id', 'price', 'stock', 'image'];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return url('uploads/' . $this->image);
        }
        return null;
    }

    public function orders()
    {
        return $this->belongsT(Order::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductsCategory::class);
    }
}
