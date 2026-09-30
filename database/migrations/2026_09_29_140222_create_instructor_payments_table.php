<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instructor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->decimal('amount', 15, 2); // ငွေပမာဏ မပါရင် မဖြစ်လို့ ထည့်ပေးထားပါတယ်
            $table->date('payment_date');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('instructor_payments');
    }
};
