<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPackageDiscount extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'discount_amount',
        'expiration_date',
        'expiration_time',
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
