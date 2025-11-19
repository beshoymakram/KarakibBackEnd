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
        'description',
        'proof'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];


    protected $appends = ['proof_url'];

    public function getProofUrlAttribute()
    {
        if ($this->proof) {
            return url('uploads/' . $this->proof);
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
