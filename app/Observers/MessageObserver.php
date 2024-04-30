<?php

namespace App\Observers;

use App\Models\Assistant;
use App\Models\Message;
use App\Models\MessageRecommendation;
use App\Models\Thread;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class MessageObserver
{
    public function creating(Message $message): void
    {
        if (! $message->thread_id) {
            $this->createThreadForMessage($message);
        }
    }

    public function created(Message $message): void
    {
        $this->assignMessageToAssistant($message);
    }

    protected function createThreadForMessage(Message $message): void
    {
        try {
            $threadName = $this->generateThreadName($message);
            $description = $this->generateThreadDescription($message, $threadName);

            $thread = Thread::create(['name' => $threadName, 'description' => $description]);
            $message->thread()->associate($thread);
        } catch (\Exception $e) {
            Log::error('Failed to create thread for message: '.$e->getMessage());
        }
    }

    protected function generateThreadName(Message $message): string
    {
        return $this->getOpenAIResponse([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => "Suggest a thread name for the message: '{$message->content}'"],
            ],
        ]);
    }

    protected function generateThreadDescription(Message $message, string $threadName): string
    {
        return $this->getOpenAIResponse([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user', 'content' => "Describe the thread named '{$threadName}' based on this message: '{$message->content}'",
                ],
            ],
        ]);
    }

    protected function getOpenAIResponse(array $params): string
    {
        $response = OpenAI::chat()->create($params);

        return $response['choices'][0]['message']['content'];
    }

    protected function assignMessageToAssistant(Message $message): void
    {
        $assistants = Assistant::select('id', 'name', 'description')->get();
        $assistantDataJson = $assistants->toJson(JSON_PRETTY_PRINT);

        $response = $this->getOpenAIResponse([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Evaluate the message: '{$message->content}'. Provide a JSON response with 'assistants' and 'suggestions'".
                        "\n\nCurrent Assistants Data:\n".$assistantDataJson,
                ],
            ],
        ]);

        $this->processOpenAIResponse($response, $message);
    }

    protected function processOpenAIResponse(string $responseContent, Message $message): void
    {
        $data = json_decode($responseContent, true);

        if (isset($data['assistants'])) {
            $this->processAssistants($data['assistants'], $message);
        }

        if (isset($data['suggestions'])) {
            $this->processSuggestions($data['suggestions'], $message);
        }
    }

    protected function processAssistants(array $assistants, Message $message): void
    {
        foreach ($assistants as $assistant) {
            try {
                MessageRecommendation::create([
                    'message_id' => $message->id,
                    'assistant_id' => $assistant['id'],
                    'points' => $assistant['points'],
                    'reason' => $assistant['reason'],
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create message recommendation: '.$e->getMessage());
            }
        }
    }

    protected function processSuggestions(array $suggestions, Message $message): void
    {
        foreach ($suggestions as $suggestion) {
            try {
                $newAssistant = Assistant::create([
                    'name' => $suggestion['name'],
                    'description' => $suggestion['description'],
                    'instructions' => $suggestion['instructions'],
                    'ai_model_id' => 9,
                ]);

                MessageRecommendation::create([
                    'message_id' => $message->id,
                    'assistant_id' => $newAssistant->id,
                    'points' => 0,
                    'reason' => 'New assistant created based on message content',
                ]);

                Log::info('Created new assistant: '.$suggestion['name']);
            } catch (\Exception $e) {
                Log::error('Failed to create new assistant: '.$suggestion['name']);
            }
        }
    }
}
