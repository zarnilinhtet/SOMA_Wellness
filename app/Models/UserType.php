<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'permissions'];

    // JSON ကို Array အဖြစ် auto ပြောင်းပေးရန်
    protected $casts = [
        'permissions' => 'array',
    ];
    // User များနှင့် ချိတ်ဆက်မှု
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
