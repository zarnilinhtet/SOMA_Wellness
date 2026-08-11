<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorCategoryFee extends Model
{
    protected $fillable = ['instructor_id', 'category_id', 'fee_type', 'fee_value'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
