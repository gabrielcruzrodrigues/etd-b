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
            $table->string('name', 255)->nullable(false)->unique()->change();
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->string('name', 255)->nullable(false)->unique()->change();
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->string('name', 255)->nullable(false)->unique()->change();
        });

        Schema::table('subtopics', function (Blueprint $table) {
            $table->string('name', 255)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matters', function (Blueprint $table) {
            $table->string('name', 80)->nullable(false)->unique()->change();
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->string('name', 80)->nullable(false)->unique()->change();
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->string('name', 80)->nullable(false)->unique()->change();
        });

        Schema::table('subtopics', function (Blueprint $table) {
            $table->string('name', 80)->nullable(false)->unique()->change();
        });
    }
};
