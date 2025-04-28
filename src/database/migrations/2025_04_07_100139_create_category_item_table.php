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
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // 複合ユニーク制約（item_idとuser_idの組み合わせが重複しない）
            // 同じitemに同じcategoryを重複登録しない
            $table->unique(['item_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_item');
    }
}
