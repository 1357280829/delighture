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
            $table->string('account')->unique()->comment('账号');
            $table->string('password')->comment('密码');
            $table->string('nickname')->comment('昵称');
            $table->string('phone')->nullable()->comment('手机号');
            $table->string('email')->nullable()->comment('邮箱');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
