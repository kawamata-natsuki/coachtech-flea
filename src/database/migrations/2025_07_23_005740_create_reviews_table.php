<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('reviewee_id');
            $table->tinyInteger('rating'); // 星1〜5
            $table->timestamps();

            // 複合ユニーク
            $table->unique(['order_id', 'reviewer_id']);

            // 外部キー制約
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('reviewee_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
