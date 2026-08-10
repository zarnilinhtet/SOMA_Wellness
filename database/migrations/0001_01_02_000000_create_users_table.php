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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->integer('coins')->default(0); // User ရဲ့ coin balance ကို သိမ်းဆည်းရန် field အသစ်
            $table->date('age');
            $table->integer('discount')->default(0);
            $table->date('discount_expire_at')->nullable(); // User ရဲ့ discount expire date ကို သိမ်းဆည်းရန် field အသစ်
            $table->json('packages')->nullable(); // User ရဲ့ packages ကို သိမ်းဆ
            $table->string('avatar')->nullable(); // User ရဲ့ profile picture ကို သိမ်းဆည်းရန် field အသစ်
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            //  User Type (Role) နှင့် ချိတ်ဆက်ရန် Foreign Key ကို ထည့်သွင်းထားပါသည်
            $table->foreignId('user_type_id')->nullable()->constrained('user_types')->onDelete('set null');

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('phone')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
