<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends BaseModel
{
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
