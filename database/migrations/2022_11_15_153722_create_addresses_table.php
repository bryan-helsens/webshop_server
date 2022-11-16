<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string("street1");
            $table->string("street2")->nullable();
            $table->string("city");
            $table->string("country");
            $table->string("state");
            $table->integer("zipcode");

            $table->unsignedBigInteger('type_id');
            //$table->integer("type")->default('0'); // 0 = shipping address, 1 = billing address
            $table->foreign('type_id')->references('id')->on('type_address')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
