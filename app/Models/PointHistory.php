<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    protected $table = 'point_history';

    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description'
    ];

    protected $casts = [
        'points' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
