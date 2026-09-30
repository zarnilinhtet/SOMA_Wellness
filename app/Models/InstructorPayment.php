<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'remark'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
