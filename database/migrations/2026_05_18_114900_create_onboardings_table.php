<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('onboardings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('starting_level');
            $table->json('included_practices');
            $table->json('preferred_times');
            $table->json('considerations');
            $table->string('know_where');
            $table->string('selected_plan')->nullable();
            $table->boolean('rules_accepted')->default(false);
            $table->boolean('payment_policy_accepted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboardings');
    }
};
