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
        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->unique();
            $table->longText('question');
            $table->string('path_image', 100);
            $table->longText('answer');
            $table->foreignId('matter_id')->constrained('matters')->onDelete('cascade');
            $table->foreignId('content_id')->constrained('contents')->onDelete('cascade');
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->foreignId('subtopic_id')->nullable()->constrained('subtopics')->onDelete('cascade');
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->enum('state', ['active', 'disabled', 'revision'])->default('disabled');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcards');
    }
};
