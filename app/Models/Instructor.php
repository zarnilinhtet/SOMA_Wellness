<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['instructor_id', 'specialty', 'total_earnings'];

    public function user()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function categoryFees()
    {
        return $this->hasMany(InstructorCategoryFee::class, 'instructor_id');
    }
}