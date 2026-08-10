<?php

namespace App\Exports;

use App\Models\ProductVariation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize, WithCustomValueBinder
{
    public function collection()
    {
        // Category နဲ့ Unit Name တွေရဖို့ Relationship တွေကိုပါ Eager Load လုပ်ပါမယ်
        return ProductVariation::with(['product.category', 'product.unit'])->get();
    }

    public function headings(): array
    {
        return [
            'Product Name',   // A
            'Category Name',  // B (ID အစား Name ပြောင်းထားသည်)
            'Unit Name',      // C (ID အစား Name ပြောင်းထားသည်)
            'Variant Name',   // D
            'Size',           // E
            'Color',          // F
            'Barcode',        // G
            'Purchase Price', // H
            'Sale Price',     // I
            'Stock Qty'       // J
        ];
    }

    public function map($variation): array
    {
        return [
            $variation->product->name ?? '',
            $variation->product->category->category_name ?? '', // Category Name ကို ယူမယ်
            $variation->product->unit->unit_name ?? '',         // Unit Name ကို ယူမယ်
            $variation->variant_name,
            $variation->size,
            $variation->color,
            $variation->barcode,
            $variation->purchase_price,
            $variation->sale_price,
            $variation->stock_qty,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_NUMBER_00,
            'I' => NumberFormat::FORMAT_NUMBER_00,
            'J' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value) && str_starts_with($value, '=')) {
            $cell->setValueExplicit($value, DataType::TYPE_FORMULA);
            return true;
        }

        if ($cell->getColumn() == 'G') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
