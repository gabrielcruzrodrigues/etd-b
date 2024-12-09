<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_plains', function (Blueprint $table) {
            $table->id();
            $table->date('start');
            $table->date('exame_date');
            $table->enum('desired_course', ['course1', 'course2', 'course3']); 
            $table->longText('dificulty_matters'); 
            $table->enum('day_off', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->integer('hours_study_day');
            $table->integer('exame_expirence');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('study_plains');
    }
};