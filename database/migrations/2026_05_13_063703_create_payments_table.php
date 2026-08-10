<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Daw Aye Aye (Account Name)
            $table->string('method'); // e.g., KBZ Pay, CB Bank, Wave Money
            $table->string('account_info'); // e.g., 09123456789 or 1234-5678-9012
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // For QR code or Bank Logo
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};