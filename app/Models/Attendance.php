<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'instructor_id',
        'client_id',
        'class_id',
        'attendance_date',
        'fee_amount',
        'attended',
        'admin_approve'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassSchedule::class);
    }
}
