<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('instructor_category_fees', function (Blueprint $table) {
            // အရင် Bonus column တွေရှိနေရင် ဖျက်ပါမည်
            if (Schema::hasColumn('instructor_category_fees', 'bonus_threshold')) {
                $table->dropColumn(['bonus_threshold', 'bonus_amount']);
            }

            // Tiers အများကြီးသိမ်းဖို့ JSON column အသစ်ထည့်ပါမည်
            $table->json('bonuses')->nullable()->after('fee_value');
        });
    }

    public function down(): void
    {
        Schema::table('instructor_category_fees', function (Blueprint $table) {
            $table->dropColumn('bonuses');

            // Rollback လုပ်ရင် နဂိုအတိုင်းပြန်ထားရန်
            $table->integer('bonus_threshold')->default(0);
            $table->decimal('bonus_amount', 12, 2)->default(0);
        });
    }
};
