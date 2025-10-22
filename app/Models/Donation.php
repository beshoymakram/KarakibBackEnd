<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [
        'donation_number',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'amount',
        'status',
        'fund_name'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public static function generateNumber()
    {
        return 'DON-' . strtoupper(uniqid());
    }
}
