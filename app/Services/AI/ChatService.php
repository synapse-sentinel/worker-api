<?php

namespace App\Services\AI;

use App\Models\Chat;
use App\Models\Message;
use EchoLabs\Prism\Prism;
use Illuminate\Support\Facades\Log;
use EchoLabs\Prism\Enums\Provider;

class ChatService
{
    protected ModelSelector $modelSelector;

    public function __construct(ModelSelector $modelSelector)
    {
        $this->modelSelector = $modelSelector;
    }

    public function sendMessage(Chat $chat, string $message, array $context = []): Message
    {
        try {
            // Select the appropriate model
            $model = $this->modelSelector->selectModel($message, $context);

            // Store user message
            $userMessage = $chat->messages()->create([
                'role'     => 'user',
                'content'  => $message,
                'metadata' => ['model' => $model],
            ]);

            // Get chat history for context
            $history = $this->formatChatHistory($chat);

            // Configure provider based on model
            $provider = str_contains($model, 'claude') ? 'anthropic' : 'openai';


            $response  = Prism::text()
                ->using(Provider::OpenAI, 'gpt-3.5-turbo')
                ->withPrompt($message)->generate();


                // Store AI response
            $aiMessage = $chat->messages()->create([
                'role'     => 'assistant',
                'content'  => $response->text,
                'metadata' => [
                    'model'    => $model,
                    'provider' => $provider,
                    'usage'    => $response->usage ?? null,
                ],
            ]);

            return $aiMessage;

        } catch (\Exception $e) {
            Log::error('AI Chat Error', [
                'error'   => $e->getMessage(),
                'chat_id' => $chat->id,
                'model'   => $model ?? null,
            ]);

            // Try fallback model if available
            if (isset($model) && $fallbackModel = $this->modelSelector->getFallbackModel($model)) {
                return $this->sendMessageWithModel($chat, $message, $fallbackModel, $context);
            }

            throw $e;
        }
    }

    protected function formatChatHistory(Chat $chat): array
    {
        return $chat->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($message) => [
                'role'    => $message->role,
                'content' => $message->content,
            ])->toArray();
    }

    protected function sendMessageWithModel(Chat $chat, string $message, string $model, array $context = []): Message
    {
        $provider = str_contains($model, 'claude') ? 'anthropic' : 'openai';

        $response = Prism::text()->using(Provider::OpenAI, 'gpt-3.5-turbo')
            ->withPrompt($message)->generate();


        return $chat->messages()->create([
            'role'     => 'assistant',
            'content'  => $response->text,
            'metadata' => [
                'model'    => $model,
                'provider' => $provider,
                'usage'    =>  $response->usage ?? null,
                'fallback' => true,
            ],
        ]);
    }
}
