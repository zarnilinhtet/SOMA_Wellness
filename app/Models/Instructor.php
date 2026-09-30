<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function payments()
    {
        return $this->hasMany(InstructorPayment::class)->latest();
    }

    // Data အသစ်မသိမ်းခင် အလိုအလျောက် အလုပ်လုပ်မည့် Function
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Get the creation date (fallback to current time if created_at is not set yet)
            $date = $model->created_at ?? now();

            // Format to YYMM (e.g., "2608" for August 2026)
            $prefix = $date->format('ym');

            // Find the last generated code for this specific month
            $latest = self::where('instructor_code', 'like', $prefix . '%')
                ->orderBy('instructor_code', 'desc')
                ->first();

            // If there's a record this month, increment the serial. Otherwise, start at 1.
            if ($latest) {
                // Extract the serial number part (skip the first 4 characters "YYMM")
                $lastSerial = (int) substr($latest->instructor_code, 4);
                $nextId = $lastSerial + 1;
            } else {
                $nextId = 1;
            }

            // Combine prefix and 3-digit padded serial (e.g., 2608001)
            $model->instructor_code = $prefix . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function categoryFees()
    {
        return $this->hasMany(InstructorCategoryFee::class, 'instructor_id');
    }
}
