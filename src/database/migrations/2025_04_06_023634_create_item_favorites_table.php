<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemFavoritesTable extends Migration
{
    public function up()
    {
        Schema::create('item_favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // 外部キー制約
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


            // 複合ユニーク制約（item_idとuser_idの組み合わせが重複しない）
            // 同じユーザーが同じ商品に複数回いいねできない
            $table->unique(['item_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_favorites');
    }
}
