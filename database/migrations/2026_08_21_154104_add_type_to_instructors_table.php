<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->string('instructor_type')->default('full_time')->after('specialty');
        });
    }


    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn('instructor_type');
        });
    }
};
