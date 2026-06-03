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
        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn('delay_code');
            $table->foreignId('delay_code_id')->nullable()->constrained('delay_codes');
            $table->foreignId('flight_schedule_id')->nullable()->constrained('flight_schedules');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropForeign(['delay_code_id']);
            $table->dropForeign(['flight_schedule_id']);
            $table->dropColumn(['delay_code_id', 'flight_schedule_id']);
            $table->string('delay_code')->nullable();
        });
    }
};
