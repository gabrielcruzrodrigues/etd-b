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
        Schema::create('spaced_studies', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable(false);
            $table->foreignId('register_study_id')->constrained('register_studies')->onDelete('cascade');
            $table->boolean('completed')->nullable(false);
            $table->time('conclusion_study_time')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaced_studies');
    }
};
