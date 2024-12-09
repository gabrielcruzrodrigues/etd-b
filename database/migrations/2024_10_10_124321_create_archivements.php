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
        Schema::create('archivements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('questions_level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('comments_level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('flashcard_level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('flashcard_created_level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('simulated_level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('essay_level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('study_record_level', ['beginner', 'intermediate', 'advanced']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivements');
    }
};
