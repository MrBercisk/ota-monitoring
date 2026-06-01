<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delay_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('reason');
            $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delay_codes');
    }
};