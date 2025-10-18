<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WasteItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'waste_type_id',
        'name',
        'points_per_unit',
        'unit',
        'image',
    ];

    protected $casts = [
        'points_per_unit' => 'decimal:2'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return url('uploads/' . $this->image);
        }
        return null;
    }


    public function wasteType()
    {
        return $this->belongsTo(WasteType::class, 'waste_type_id');
    }
}
