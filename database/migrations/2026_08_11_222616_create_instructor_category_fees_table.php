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
        Schema::create('instructor_category_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // adjust table name if needed
            $table->enum('fee_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('fee_value', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['instructor_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_category_fees');
    }
};
