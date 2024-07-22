<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_zones', function (Blueprint $table) {
            $table->dropColumn('currency');
            $table->dropColumn('currency_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_zones', function (Blueprint $table) {
            $table->string('currency')->nullable();
            $table->string('currency_rate')->nullable();
        });
    }
};
