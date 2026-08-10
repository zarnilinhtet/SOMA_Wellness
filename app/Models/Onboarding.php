<?php

namespace App\Models;

// ✨ FIX: Eloquent စာသားပါဝင်သော မှန်ကန်သည့် လမ်းကြောင်းသို့ ပြောင်းလဲလိုက်ပါသည်
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Onboarding extends Model
{
    protected $fillable = [
        'user_id',
        'starting_level',
        'included_practices',
        'preferred_times',
        'considerations',
        'know_where',
        'selected_plan',
        'rules_accepted',
        'payment_policy_accepted',
    ];

    // Checkbox Array များကို Database သို့သွင်းစဉ် JSON ပြောင်းရန်နှင့် ပြန်ထုတ်လျှင် Array ပြန်ပြောင်းရန်
    protected $casts = [
        'included_practices' => 'array',
        'preferred_times' => 'array',
        'considerations' => 'array',
        'payment_policy_accepted' => 'boolean',
    ];

    /**
     * အဆိုပါ Onboarding ဒေတာ ပိုင်ဆိုင်သော User အား ချိတ်ဆက်ခြင်း
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
