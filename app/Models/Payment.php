<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // method ကို ထပ်မံဖြည့်စွက်ထားပါသည်
    protected $fillable = ['name', 'method', 'account_info', 'description', 'image'];
}   