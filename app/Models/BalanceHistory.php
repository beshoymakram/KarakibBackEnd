<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceHistory extends BaseModel
{
    protected $table = 'balance_history';

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'status',
        'wallet_number',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
