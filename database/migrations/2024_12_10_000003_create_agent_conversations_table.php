<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents');
            $table->foreignId('user_id')->constrained('users');
            $table->string('title')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('agent_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('agent_conversations');
            $table->string('role'); // system, user, assistant
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_messages');
        Schema::dropIfExists('agent_conversations');
    }
};
