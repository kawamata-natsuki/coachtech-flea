<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryItemTable extends Migration
{
    public function up()
    {
        Schema::create('category_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            // 外部キー制約
            // 商品が削除されたら、関連するカテゴリの紐付けも一緒に削除される
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            // カテゴリが削除されたら、関連する商品との紐付けも一緒に削除される
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // 複合ユニーク制約
            // 同じ商品に同じカテゴリを重複して紐付けられない
            $table->unique(['item_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_item');
    }
}
