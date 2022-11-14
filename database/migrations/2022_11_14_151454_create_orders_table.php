<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string("firstname");
            $table->string("lastname");
            $table->string("phone");
            $table->string("email");
            $table->string("street1");
            $table->string("street2")->nullable();
            $table->string("city");
            $table->string("country");
            $table->string("state");
            $table->integer("zipcode");
            $table->string("payment_id")->nullable();
            $table->string("payment_method")->nullable();
            $table->double("subtotal");
            $table->double("shipping_price")->nullable();
            $table->double("tax_price");
            $table->double("total_price");

            $table->boolean("is_paid")->default(false);
            $table->date("paid_at")->nullable();

            $table->boolean("is_delivered")->default(false);
            $table->date("delivered_at")->nullable();

            $table->tinyInteger('status')->default("0"); // 0 = pending, 1 = completed, 2 = cancelled

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
        Schema::dropIfExists('orders');
    }
}
