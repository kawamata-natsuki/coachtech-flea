<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionsTable extends Migration
{
    public function up()
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 状態の表示名
            $table->string('code')->unique(); // 状態のコード
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conditions');
    }
}
