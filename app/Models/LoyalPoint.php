<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyalPoint extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'points',
        'is_redeemed',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
