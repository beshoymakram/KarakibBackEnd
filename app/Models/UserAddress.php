<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = ['user_id', 'name', 'street_address', 'city', 'phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
