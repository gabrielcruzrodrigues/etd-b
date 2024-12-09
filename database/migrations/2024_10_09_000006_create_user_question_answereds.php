<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_question_answereds', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->nullable(false);
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade')->nullable(false);

            $table->enum('alternative', ['A', 'B', 'C', 'D', 'E'])->nullable(false);           
            $table->enum('error_notebook', ['certainty', 'content', 'interpretation', 'distraction', 'kicked'])->nullable(); 
            $table->timestamps();

            $table->primary(['user_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_question_answereds');
    }
};
