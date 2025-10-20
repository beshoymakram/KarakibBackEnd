<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsCategory extends Model
{
    protected $table = 'products_categories';
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
