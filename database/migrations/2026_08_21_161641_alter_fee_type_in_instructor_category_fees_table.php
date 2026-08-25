<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // fee_type ကို စာလုံးရေ ၅၀ အထိ ဆန့်တဲ့ VARCHAR အဖြစ် ပြောင်းလဲမည်
        DB::statement("ALTER TABLE instructor_category_fees MODIFY fee_type VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // အကယ်၍ Rollback ပြန်လုပ်ပါက မူလအတိုင်း ပြန်ထားရန် (လိုအပ်ပါက ဤနေရာတွင် ပြင်နိုင်သည်)
        // DB::statement("ALTER TABLE instructor_category_fees MODIFY fee_type ENUM('fixed', 'percentage') NOT NULL");
    }
};
