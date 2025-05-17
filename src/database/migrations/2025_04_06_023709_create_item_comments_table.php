<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('item_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('user_id');
            $table->string('content'); // コメント最大255文字
            $table->timestamps();

            // 外部キー制約
            // 商品が削除されたら、関連するコメントも一緒に削除される
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            // ユーザーが削除されたら、関連するコメントも一緒に削除される
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_comments');
    }
}
