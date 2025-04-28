<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable(); //メール認証で必要
            $table->string('password');
            $table->string('postal_code')->nullable(); // ハイフンつき7桁の郵便番号
            $table->string('address')->nullable();
            $table->string('building')->nullable(); // 確認中
            $table->string('profile_image')->nullable(); // 画像はstorageに保存し、DBにはファイルパスのみ保持
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
