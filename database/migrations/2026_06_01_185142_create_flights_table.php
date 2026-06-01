<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->date('flight_date');
            $table->string('flight_number');   // MH712, MH726, dll
            $table->foreignId('station_id')->constrained();
            $table->time('sta');               // Scheduled Time Arrival
            $table->time('std');               // Scheduled Time Departure
            $table->time('ata')->nullable();   // Actual Time Arrival
            $table->time('atd')->nullable();   // Actual Time Departure
            $table->integer('delay_minutes')->default(0);
            $table->string('delay_code')->nullable();  // RA, AT, dll
            $table->enum('status', ['on_time', 'delayed', 'night_stop'])->default('on_time');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
