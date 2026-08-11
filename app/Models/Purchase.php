<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //
    protected $fillable = ['registered_id', 'selected_packages_id', 'account_name', 'phone','receiver_name','receiver_phone', 'transaction_no', 'payment_method', 'pay_status', 'amount','coin_used','user_discount', 'class_remaining', 'expires_at','fix_expires_at', 'rejection_reason', 'screenshot'];
    protected $casts = [
        'expires_at' => 'datetime',
        'fix_expires_at' => 'datetime',
    ];
    public function package()
    {
        return $this->belongsTo(Package::class, 'selected_packages_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'registered_id');
    }
}
