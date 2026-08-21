<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorCategoryFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'fee_type',
        'fee_value',
        'bonuses',
    ];

    // JSON ကို Array အဖြစ် အလိုအလျောက်ပြောင်းပေးရန်
    protected $casts = [
        'bonuses' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
}
