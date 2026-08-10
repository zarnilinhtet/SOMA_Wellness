<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB;

class ProductImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {

                if (empty($row['product_name']) || empty($row['variant_name'])) {
                    continue;
                }

                $productName = trim($row['product_name']);
                $variantName = trim($row['variant_name']);
                $barcode     = isset($row['barcode']) ? trim((string)$row['barcode']) : null;
                $excelStock  = (int) ($row['stock_qty'] ?? 0); // Excel ထဲက Qty ကို ယူမယ်

                // ၁။ Category & Unit
                $categoryName = isset($row['category_name']) ? trim($row['category_name']) : '';
                $category = Category::where('category_name', $categoryName)->first();
                if (!$category) throw new \Exception("Category '{$categoryName}' မရှိပါ။");

                $unitName = isset($row['unit_name']) ? trim($row['unit_name']) : '';
                $unit = Unit::where('unit_name', $unitName)->first();
                if (!$unit) throw new \Exception("Unit '{$unitName}' မရှိပါ။");

                // ၂။ Product ကို အရင်ရှာ (Soft Delete ပါဝင်သည်)
                $product = Product::withTrashed()->where('name', $productName)->first();
                if ($product) {
                    if ($product->trashed()) $product->restore();
                    $product->update(['category_id' => $category->id, 'unit_id' => $unit->id]);
                } else {
                    $product = Product::create([
                        'name' => $productName,
                        'category_id' => $category->id,
                        'unit_id' => $unit->id,
                        'product_qty' => 0
                    ]);
                }

                // ၃။ Variation ကို အရင်ဆုံး ရှာဖွေခြင်း
                $variation = null;
                if (!empty($barcode)) {
                    $variation = ProductVariation::withTrashed()->where('barcode', $barcode)->first();
                }

                if (!$variation) {
                    $variation = ProductVariation::withTrashed()
                        ->where('product_id', $product->id)
                        ->where('variant_name', $variantName)
                        ->first();
                }

                // ၄။ Qty ကို ပေါင်းထည့်မည့် Logic
                if ($variation) {
                    if ($variation->trashed()) $variation->restore();

                    // *** အရေးကြီးဆုံးအပိုင်း - လက်ရှိ Qty ထဲကို Excel က Qty ပေါင်းထည့်မယ် ***
                    $newStock = $variation->stock_qty + $excelStock;

                    $variation->update([
                        'size'           => $row['size'] ?? $variation->size,
                        'color'          => $row['color'] ?? $variation->color,
                        'barcode'        => $barcode,
                        'purchase_price' => (float) ($row['purchase_price'] ?? $variation->purchase_price),
                        'sale_price'     => (float) ($row['sale_price'] ?? $variation->sale_price),
                        'stock_qty'      => $newStock, // ပေါင်းလဒ်အသစ်ကို ထည့်မယ်
                    ]);
                } else {
                    // အသစ်ဆိုရင်တော့ Excel ထဲက အတိုင်းပဲ တိုက်ရိုက်ထည့်မယ်
                    ProductVariation::create([
                        'product_id'     => $product->id,
                        'variant_name'   => $variantName,
                        'size'           => $row['size'] ?? null,
                        'color'          => $row['color'] ?? null,
                        'barcode'        => $barcode,
                        'purchase_price' => (float) ($row['purchase_price'] ?? 0),
                        'sale_price'     => (float) ($row['sale_price'] ?? 0),
                        'stock_qty'      => $excelStock,
                    ]);
                }

                // ၅။ Master Product Qty ကို အမှန်ဖြစ်အောင် Sum ပြန်လုပ်မယ်
                $totalStock = ProductVariation::where('product_id', $product->id)->sum('stock_qty');
                $product->update(['product_qty' => $totalStock]);
            }
        });
    }
}
