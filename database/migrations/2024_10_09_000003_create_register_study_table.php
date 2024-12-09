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
        Schema::create('register_studies', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('study_plain_id')->constrained('study_plains')->onDelete('cascade');
            $table->foreignId('matter_id')->constrained('matters')->onDelete('cascade');
            $table->foreignId('content_id')->constrained('contents')->onDelete('cascade');
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->foreignId('subtopic_id')->constrained('subtopics')->onDelete('cascade');
            $table->time('conclusion_study_time');
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
        Schema::dropIfExists('register_studys');
    }
};
