<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Purchase တစ်ခုတွင် Items များစွာပါဝင်နိုင်သည်
    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }
}
