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
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('shipping_address');
            $table->foreign('shipping_address')->references('id')->on('addresses')->onDelete('cascade');
            $table->unsignedBigInteger('billing_address');
            $table->foreign('billing_address')->references('id')->on('addresses')->onDelete('cascade');

            $table->string("order_date");

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

            $table->tinyInteger('order_status')->default("0"); // 0 = pending, 1 = completed, 2 = cancelled

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
