<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('h:i A / Y-m-d');
    }
}
