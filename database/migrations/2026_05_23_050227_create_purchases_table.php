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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registered_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('selected_packages_id')->constrained('packages')->onDelete('cascade');
            $table->string('account_name');
            $table->string('phone');
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('transaction_no')->nullable();
            $table->string('screenshot')->nullable();
            $table->string('payment_method');
            $table->string('amount');
            $table->integer('coin_used')->nullable();
            $table->integer('user_discount')->nullable();
            $table->string('pay_status')->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->integer('class_remaining')->default(0);
            $table->dateTime('expires_at');
            $table->dateTime('fix_expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
