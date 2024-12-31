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
        Schema::table('matters', function (Blueprint $table) {
            $table->dropUnique('matters_name_unique'); // Remove o índice único da coluna 'name'
        });

        // Alterar a tabela 'contents'
        Schema::table('contents', function (Blueprint $table) {
            $table->dropUnique('contents_name_unique'); // Remove o índice único da coluna 'name'
        });

        // Alterar a tabela 'topics'
        Schema::table('topics', function (Blueprint $table) {
            $table->dropUnique('topics_name_unique'); // Remove o índice único da coluna 'name'
        });

        // Alterar a tabela 'subtopics'
        Schema::table('subtopics', function (Blueprint $table) {
            $table->dropUnique('subtopics_name_unique'); // Remove o índice único da coluna 'name'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matters', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('subtopics', function (Blueprint $table) {
            $table->unique('name');
        });
    }
};
