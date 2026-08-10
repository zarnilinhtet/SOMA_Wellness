<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['instructor_id', 'specialty','fee', 'total_earnings'];

    public function user()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}