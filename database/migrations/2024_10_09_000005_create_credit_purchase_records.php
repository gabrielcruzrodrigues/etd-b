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
        Schema::create('credit_purchase_records', function (Blueprint $table) {
            $table->id();
            $table->dateTime('buy_date')->nullable(false);             
            $table->dateTime('expiration_date')->nullable(false);      
            $table->integer('value')->nullable(false);                
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_purchase_records');
    }
};
