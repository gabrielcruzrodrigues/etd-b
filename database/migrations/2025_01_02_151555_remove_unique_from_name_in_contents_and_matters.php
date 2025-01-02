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
        Schema::table('contents', function (Blueprint $table) {
            $table->dropUnique('contents_name_unique'); // Nome do índice no banco de dados
        });

        // Remove o índice único da coluna 'name' na tabela 'matters'
        Schema::table('matters', function (Blueprint $table) {
            $table->dropUnique('matters_name_unique'); // Nome do índice no banco de dados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->unique('name', 'contents_name_unique');
        });

        // Recria o índice único na coluna 'name' na tabela 'matters'
        Schema::table('matters', function (Blueprint $table) {
            $table->unique('name', 'matters_name_unique');
        });
    }
};
