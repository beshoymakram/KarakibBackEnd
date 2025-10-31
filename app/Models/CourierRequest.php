<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierRequest extends Model
{
    protected $fillable = ['courier_id', 'request_id'];

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id')->withTrashed();
    }

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id')->withTrashed();
    }
}
