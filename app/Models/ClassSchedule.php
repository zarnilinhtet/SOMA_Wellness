<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_ids',
        'category_ids',
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
        'category_ids' => 'array',
        'days' => 'array',
    ];

    public function instructor()
    {
        return $this->hasMany(Instructor::class, 'instructor_id', 'instructor_ids');
    }

    // Custom Category relationship using category_ids JSON array
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->withDefault();
    }

    // Helper method to get categories collection
    public function getCategoriesAttribute()
    {
        $ids = is_string($this->category_ids) ? json_decode($this->category_ids, true) : ($this->category_ids ?? []);
        if (!is_array($ids)) $ids = [$ids];

        return Category::whereIn('id', $ids)->get();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'selected_class_id', 'id')
            ->where('status', '!=', 'cancelled');
    }
}
