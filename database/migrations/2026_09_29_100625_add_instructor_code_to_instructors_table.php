<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // instructor_code အဖြစ် column အသစ်ထည့်ခြင်း
            $table->string('instructor_code')->nullable()->after('maintenance_fees');
        });
    }

    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn('instructor_code');
        });
    }
};
