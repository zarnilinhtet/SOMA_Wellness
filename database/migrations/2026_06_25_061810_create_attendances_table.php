<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('class_schedules')->onDelete('cascade');
            $table->date('attendance_date');
            $table->integer('fee_amount')->default(0);
            $table->boolean('attended')->default(0);
            $table->boolean('admin_approve')->default(0);
            $table->boolean('is_paid')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
