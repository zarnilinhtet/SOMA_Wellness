<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->string('payment_date')->nullable()->after('specialty');
          
            $table->string('payment_method')->nullable()->after('payment_date');
            $table->decimal('maintenance_fees', 12, 2)->default(0)->after('payment_method');
            $table->decimal('class_teaching_fees', 12, 2)->default(0)->after('maintenance_fees');
         
        });
    }

    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn(['payment_date', 'payment_method', 'maintenance_fees', 'class_teaching_fees']);
        });
    }
};
