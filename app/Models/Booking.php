<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    protected $guarded = [];
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function bookingUser()
    {
        return $this->belongsTo(User::class, 'registered_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassSchedule::class, 'selected_class_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'registered_id', 'id');
    }
    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'selected_class_id');
    }
}
