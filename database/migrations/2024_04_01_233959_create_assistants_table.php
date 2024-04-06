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
        Schema::create('assistants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256);
            $table->string('avatar',
                256)->unique()->nullable();
            $table->string('description', 512)->nullable();
            $table->foreignId('ai_model_id')->constrained();
            $table->text('instructions')->nullable();
            $table->string('provider')->default('openai');
            $table->string('provider_value')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistants');
    }
};
