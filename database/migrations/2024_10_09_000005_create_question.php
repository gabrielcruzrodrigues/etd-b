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
        Schema::create('years', function (Blueprint $table) {
            $table->id();
            $table->string('year', 10)->unique()->nullable(false);
            $table->timestamps();
        });

        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique()->nullable(false);
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('original_code', 50)->nullable()->unique();
            $table->string('code', 50)->unique()->nullable(false);
            $table->longText('query')->nullable(false);
            $table->text('alternative_a')->nullable(true);
            $table->text('alternative_b')->nullable(true);
            $table->text('alternative_c')->nullable(true);
            $table->text('alternative_d')->nullable(true);
            $table->text('alternative_e')->nullable(true);
            $table->char('answer', 1)->nullable(false);
            $table->boolean('alternative_has_html')->nullable(false)->default(false);
            $table->foreignId('matter_id')->constrained('matters')->onDelete('cascade');
            $table->foreignId('content_id')->nullable()->constrained('contents')->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()->constrained('topics')->onDelete('cascade');
            $table->foreignId('subtopic_id')->nullable()->constrained('subtopics')->onDelete('cascade');
            $table->enum('difficulty', ['easy', 'intermediary', 'hard'])->nullable(false);
            $table->foreignId('year_id')->nullable()->constrained('years')->onDelete('cascade');
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->onDelete('cascade');
            $table->enum('state', ['active', 'disable', 'revision']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
        Schema::dropIfExists('years');
        Schema::dropIfExists('institutions');
    }
};
