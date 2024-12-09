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
  
        Schema::create('area_of_knowledges', function (Blueprint $table) {
            $table->id(); 
            $table->string('name');
            $table->timestamps();
        });


        Schema::create('simulations', function (Blueprint $table) {
            $table->id(); 
            $table->string('image_path');
            $table->string('title');

            $table->foreignId('area_of_knowledge_id')->nullable()->constrained('area_of_knowledges')->onDelete('set null');
            $table->foreignId('matter_id')->nullable()->constrained('matters')->onDelete('set null');
            
            $table->enum('type', ['Reduzido', 'Semanal']);
            $table->timestamps();
        });

    
        Schema::create('area_of_knowledge_matter', function (Blueprint $table) {
            $table->foreignId('area_of_knowledge_id')->constrained('area_of_knowledges')->onDelete('cascade');
            $table->foreignId('matter_id')->constrained('matters')->onDelete('cascade');
            $table->primary(['area_of_knowledge_id', 'matter_id']);
        });

        Schema::create('simulation_alternatives', function (Blueprint $table) {
            $table->foreignId('simulation_id')->constrained('simulations')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->char('alternative', 1);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabela Results
        Schema::create('results', function (Blueprint $table) {
            $table->id(); // unsignedBigInteger
            $table->integer('qty_hits');
            $table->integer('qty_errors');
            $table->time('conclusion_time');
            $table->foreignId('simulation_id')->constrained('simulations')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabela Simulated_Questions (relacionamento entre simulado e questões)
        Schema::create('simulated_questions', function (Blueprint $table) {
            $table->foreignId('simulation_id')->constrained('simulations')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
    
        });

        Schema::create('user_simulation_finished', function(Blueprint $table){
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('simulation_id')->constrained('simulations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simulated_questions');
        Schema::dropIfExists('results');
        Schema::dropIfExists('simulation_alternatives');
        Schema::dropIfExists('area_of_knowledge_matter');
        Schema::dropIfExists('simulations');
        Schema::dropIfExists('area_of_knowledges');
        Schema::dropIfExists('user_simulation_finished');
    }
};