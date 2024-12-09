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
        Schema::create('essays', function (Blueprint $table) {
            $table->id();
            $table->dateTime('send_date');
            $table->string('essay_theme', length: 150);
            $table->string('link_file', length: 150);
        });

        Schema::create('essay_user_rating', function (Blueprint $table)
        {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('essay_id')->constrained('essays')->onDelete('cascade');
            $table->string('title', length: 150);
            $table->enum('status',['Corrigida','Arguardando Correção', 'Em Correção'])->default('Arguardando Correção');
            $table->string('essay_image_path', length: 100);
            $table->longText('essay_text');
            $table->integer('final_note');
            $table->longText('general_comment');
            $table->integer('compentence_1');
            $table->integer('compentence_2');
            $table->integer('compentence_3');
            $table->integer('compentence_4');
            $table->integer('compentence_5');
        });

    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'essays');
        Schema::dropIfExists(table: 'essay_user_rating');

    }
};
