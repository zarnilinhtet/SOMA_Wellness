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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('class_schedules')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // The magic column: Nullable because top-level comments have no parent
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('comments')
                ->onDelete('cascade');

            $table->text('content');
            $table->boolean('admin_approval')->default(false);
            $table->timestamps();
            $table->index('class_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
