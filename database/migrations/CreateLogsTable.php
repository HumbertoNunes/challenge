<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public static function up(Builder $builder)
    {
        $builder->create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->datetime('date')->default(new Expression('CURRENT_TIMESTAMP'));
            $table->string('name');
            $table->string('symbol');
            $table->decimal('open', 5, 2);
            $table->decimal('high', 5, 2);
            $table->decimal('low', 5, 2);
            $table->decimal('close', 5, 2);

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public static function down(Builder $builder)
    {
        $builder->dropIfExists('logs');
    }
}
