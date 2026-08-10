<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();

            // Relationship IDs
            $table->json('instructor_ids');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // Category အသစ်ထည့်ထားသည်

            $table->string('class_name');
            $table->text('description')->nullable();
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();

            $table->json('days'); 

            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->integer('capacity')->default(15);
            $table->string('status')->default('upcoming');
            $table->timestamps();
        });
    }
    

    public function down()
    {
        Schema::dropIfExists('class_schedules');
    }
};