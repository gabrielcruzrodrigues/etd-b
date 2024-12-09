<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->unique();
            $table->string('image_profile')->nullable();
            $table->dateTime('payment_plan_expiration')->nullable();
            $table->integer('assay_credits')->default(0);
            $table->integer('points')->nullable()->default(0);
            $table->integer('week')->default(0);
            $table->enum('role', ['admin', 'comum', 'suporte'])->default('comum');
            $table->enum('state', ['active', 'inactive', 'suspended'])->default('active');
            $table->enum('payment_plan', ['essencial', 'mentoria', 'free'])->default('free');
            $table->enum('language', ['english', 'spanish'])->nullable()->default('english');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // fk - precisa ter as migrations feitas para colocar esses campos
            // $table->foreignId('school_id')->constrained()->onDelete('cascade');
            // $table->foreignId('study_plain')->constrained('study_plains')->onDelete('cascade');
            // $table->foreignId('course')->constrained('courses')->onDelete('cascade');


        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
