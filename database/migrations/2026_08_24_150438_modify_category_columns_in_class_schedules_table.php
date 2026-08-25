<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            // ၁။ ယခင် Category Foreign Key နှင့် Column ကို ဖျက်ပါမည်
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');

            // ၂။ Multiple Category သိမ်းရန် category_ids (JSON) ကို အသစ်ထည့်ပါမည်
            // (Data အဟောင်းတွေ Error မတက်စေရန် nullable() ထည့်ထားပေးပါသည်)
            $table->json('category_ids')->nullable()->after('instructor_ids');
        });
    }

    public function down()
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            // အကယ်၍ Rollback လုပ်ခဲ့လျှင် မူလအတိုင်း ပြန်ဖြစ်စေရန်
            $table->dropColumn('category_ids');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
        });
    }
};
