<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WasteType extends BaseModel
{
    use SoftDeletes;
    protected $fillable = ['name', 'image'];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return url('uploads/' . $this->image);
        }
        return null;
    }

    public function wasteItems()
    {
        return $this->hasMany(WasteItem::class, 'waste_type_id');
    }
}
