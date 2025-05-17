<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 商品名
            $table->string('brand')->nullable(); // ブランド名
            $table->unsignedInteger('price'); // 価格
            $table->string('description'); // 商品説明（最大255文字）
            $table->unsignedBigInteger('condition_id'); // 商品の状態
            $table->string('item_image'); // 商品画像
            $table->unsignedBigInteger('user_id'); // 出品者情報
            $table->string('item_status')->default('on_sale'); // 販売状況
            $table->timestamps();

            // 外部キー制約
            // 出品商品の状態に使われている場合は、削除できない
            $table->foreign('condition_id')->references('id')->on('conditions')->onDelete('restrict');
            // ユーザーアカウント削除時に、関連する出品商品も一緒に削除する
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
