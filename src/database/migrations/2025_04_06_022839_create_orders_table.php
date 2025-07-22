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
            $table->unsignedBigInteger('item_id')->unique(); // 1商品につき1注文まで（再購入不可）
            $table->unsignedBigInteger('user_id'); // 購入者
            $table->unsignedBigInteger('payment_method_id'); // 支払方法
            $table->string('shipping_postal_code'); // 送付先住所（郵便番号）
            $table->string('shipping_address'); // 送付先住所
            $table->string('shipping_building')->nullable(); // 送付先住所（建物名）
            $table->string('order_status')->default('pending');
            $table->timestamps();

            // 外部キー制約
            // 購入済み商品の購入情報を保持（削除不可）
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            // 購入者情報を保持（削除不可）
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            // 使用した支払い方法を保持（削除不可）
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
