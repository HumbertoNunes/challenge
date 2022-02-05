<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
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
            $table->string('name')->unique();
            $table->string('symbol')->unique();
            $table->decimal('open', 5, 2);
            $table->decimal('high', 5, 2);
            $table->decimal('low', 5, 2);
            $table->decimal('close', 5, 2);
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
