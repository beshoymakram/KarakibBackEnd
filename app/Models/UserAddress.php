<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends BaseModel
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'name', 'street_address', 'city', 'phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_address_id');
    }
}
