<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->unique(); // 1商品1販売（フリマアプリ）
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->string('shipping_postal_code');
            $table->string('shipping_address');
            $table->string('shipping_building')->nullable(); // 確認中;
            $table->timestamps();

            // 外部キー制約
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
