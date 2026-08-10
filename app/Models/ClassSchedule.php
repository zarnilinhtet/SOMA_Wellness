<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_ids',
        'category_id',
        'class_name',
        'description',
        'image_1',
        'image_2',
        'start_date',
        'days',
        'end_date',
        'start_time',
        'end_time',
        'capacity',
        'status',
        'loyal_point',
    ];

    protected $casts = [
        'instructor_ids' => 'array',
        'days' => 'array',
    ];
    public function instructor()
    {
        return $this->hasMany(Instructor::class,'instructor_id','instructor_ids');
    }

    // Category နှင့် ချိတ်ဆက်မှု
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Bookings နှင့် ချိတ်ဆက်မှု
    // public function bookings()
    // {
    //     return $this->hasMany(Booking::class, 'selected_class_id');
    // }
    public function bookings()
    {
        // Assuming 'status' contains values like 'active', 'completed', or 'cancelled'
        return $this->hasMany(Booking::class, 'selected_class_id', 'id')
            ->where('status', '!=', 'cancelled');
    }
}