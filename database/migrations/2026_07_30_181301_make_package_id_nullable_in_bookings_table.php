<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Package ID ကို Null ပေးခွင့်ပြုမည်
            $table->unsignedBigInteger('package_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id')->nullable(false)->change();
        });
    }
};
