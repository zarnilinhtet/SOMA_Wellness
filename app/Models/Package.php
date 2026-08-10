<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{

    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name', 'type', 'description', 'price', 'status', 'loyal_point', 'fix_duration', 'duration','loyal_duration', 'class_count'];
    protected $casts = [
        'duration' => 'integer',
        'fix_duration' => 'integer', 
        'loyal_duration' => 'integer',
        'classes' => 'array',
    ];
    function category()
    {
        return $this->belongsTo(Category::class, 'type', 'id');
    }

    function purchases(){
        return $this->belongsTo(Purchase::class,'id', 'selected_packages_id',);
    }

    // function classSchedules()
    // {
    //     return $this->belongsToMany(ClassSchedule::class, 'classes','class_schedule_id','package_id');
    // }
}
