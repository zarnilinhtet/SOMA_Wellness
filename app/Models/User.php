<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;
    // <-- HasRoles ကို ထည့်ပါ
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $guard_name = 'web';
    protected $fillable = [
        'name',
        'phone',
        'plain_password',
        'age',
        'discount',
        'discount_expire_at',
        'packages',
        'avatar',
        'password',
        'user_type_id',
        'coins',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'packages' => 'array',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [

            'password' => 'hashed',
        ];
    }
    // app/Models/User.php တွင် ထည့်ရန်

    // ၁။ User Type နဲ့ ချိတ်ဆက်တဲ့ Relationship
    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }
    public function packages()
    {
        // 'user_packages' နေရာမှာ သင့်ရဲ့ တကယ့် Table အမှန်နာမည်ကို အစားထိုးပါ
        // ဥပမာ - 'package_user' ဖြစ်ခဲ့လျှင်
        return $this->belongsToMany(Package::class, 'package_user');
    }
    // ၂။ Permission ရှိ/မရှိ စစ်ဆေးပေးမည့် Method
    public function hasPermission($permission)
    {
        // အကယ်၍ Super Admin ဆိုရင် Permission အကုန်ရှိတယ်လို့ သတ်မှတ်မည် (ရွေးချယ်နိုင်သည်)
        if ($this->phone === '00000') {
            return true;
        }

        // User မှာ Role (User Type) ရှိမရှိ စစ်မည်
        $userType = $this->userType;
        if (!$userType || empty($userType->permissions)) {
            return false;
        }

        // ရထားတဲ့ Permission Array ထဲမှာ Sidebar က လှမ်းစစ်တဲ့ Permission နာမည် ပါမပါ စစ်မည်
        return in_array($permission, $userType->permissions);
    }

    public function getPackagesDetailAttribute()
    {
        if (empty($this->packages) || !is_array($this->packages)) {
            return collect();
        }

        return Package::whereIn('id', $this->packages)->get();
    }

    protected $appends = ['purchases_summary'];
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'registered_id', 'id');
    }

    // Inside your User model
    public function onboarding()
    {
        return $this->hasOne(Onboarding::class, 'user_id');
    }
    public function scopeCustomersOnly($query)
    {
        return $query->whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['Admin']);
        });
    }
    //  return $query->whereDoesntHave('roles', function ($q) {
    //         $q->whereIn('name', ['Admin', 'Instructor']);
    //     });

    public function getPurchasesSummaryAttribute()
    {
        return [
            'total_packages' => $this->purchases->count(),
            'total_amount' => $this->purchases->sum('amount'),
        ];
    }
}
