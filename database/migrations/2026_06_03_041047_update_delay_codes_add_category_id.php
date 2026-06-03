<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delay_codes', function (Blueprint $table) {
            $table->dropColumn('category');                                         // hapus kolom lama
            $table->foreignId('delay_category_id')->nullable()->constrained('delay_categories');  // tambah FK
        });
    }

    public function down()
    {
        Schema::table('delay_codes', function (Blueprint $table) {
            $table->dropForeign(['delay_category_id']);
            $table->dropColumn('delay_category_id');
            $table->string('category', 100)->nullable();
        });
    }
};
