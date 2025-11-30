<?php

namespace App\Models;

use App\Services\QrCodeService;

class Request extends BaseModel
{
    protected $fillable = [
        'request_number',
        'user_id',
        'user_address_id',
        'total',
        'status',
        'payout_method',
        'courier_id',
        'qr_code',
        'collected_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public static function generateNumber()
    {
        return 'REQ-' . strtoupper(uniqid());
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id')->withTrashed();
    }

    public function assignCourier($courierId)
    {
        $this->update([
            'courier_id' => $courierId,
            'status' => 'assigned',
        ]);
    }

    public function unassignCourier()
    {
        $this->update([
            'courier_id' => null,
            'status' => 'pending',
        ]);
    }

    public function getQrCodeImageAttribute()
    {
        if (!$this->qr_code) {
            return null;
        }

        return route('request-qr.show', ['request_number' => $this->request_number]);
    }
}
