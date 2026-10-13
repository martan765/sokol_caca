<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
        });

        Schema::create('game_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
        });

        Schema::create('trainings', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('training_date');
            $table->string('location')->default('Domácí hřiště');
            $table->text('description')->nullable();
        });

        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('match_type_id');
            $table->string('home_team');
            $table->string('away_team');
            $table->dateTime('game_date');
            $table->string('location')->nullable();
            $table->string('result', 20)->nullable();

            $table->foreign('match_type_id')->references('id')->on('game_types');
        });

        Schema::create('game_attendance', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('game_id');
            $table->unsignedInteger('status_id');
            $table->text('note')->nullable();

            $table->index('user_id');
            $table->foreign('game_id')->references('id')->on('games')->cascadeOnDelete();
            $table->foreign('status_id')->references('id')->on('attendance_statuses');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_attendance');
        Schema::dropIfExists('games');
        Schema::dropIfExists('trainings');
        Schema::dropIfExists('game_types');
        Schema::dropIfExists('attendance_statuses');
    }
};