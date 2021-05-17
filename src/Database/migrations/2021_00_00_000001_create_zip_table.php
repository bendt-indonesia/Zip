<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('zip', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('province_id')->nullable();
            $table->string('province_name', 100);
            $table->unsignedInteger('city_id')->nullable();
            $table->string('city_type', 100);
            $table->string('city_name', 100);
            $table->unsignedInteger('kec_id')->nullable();
            $table->string('kec_name', 100);
            $table->unsignedInteger('kel_id')->nullable();
            $table->string('kel_name', 100);
            $table->string('zip', 15);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zip');
    }
}
