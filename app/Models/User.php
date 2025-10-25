<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'type',
        'avatar',
        'status',
        'password',
        'points',
        'balance',
        'google_id',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'type',
        'google_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return url('uploads/' . $this->avatar);
        }
        return null;
    }

    public function addPoints($points, $description = 'Earned points')
    {
        $this->points += $points;
        $this->save();

        // Record history
        PointHistory::create([
            'user_id' => $this->id,
            'points' => $points,
            'type' => 'earn',
            'description' => $description
        ]);
    }

    public function convertPoints($points)
    {
        if ($points > $this->points) {
            throw new \Exception('Insufficient points');
        }

        $rate = (1 / 19); // 1 point = 0.19 EGP
        $cash = $points * $rate;

        $this->points -= $points;
        $this->balance += $cash;
        $this->save();

        PointHistory::create([
            'user_id' => $this->id,
            'points' => -$points,
            'type' => 'convert',
            'description' => "Converted {$points} points to {$cash} EGP"
        ]);

        return $cash;
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function pointHistory()
    {
        return $this->hasMany(PointHistory::class);
    }
}
