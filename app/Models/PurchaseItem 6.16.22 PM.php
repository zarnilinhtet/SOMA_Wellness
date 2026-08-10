<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    // မည်သည့် ဘောင်ချာနှင့် သက်ဆိုင်သည်
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    // မည်သည့် ပစ္စည်း(Product) ဖြစ်သည်
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // မည်သည့် Variation (အရောင်၊ ဆိုဒ်) ဖြစ်သည်
    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
}
