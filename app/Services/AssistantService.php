<?php

namespace App\Services;

use App\Models\Assistant;
use App\Models\Message;
use App\Models\MessageRecommendation;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AssistantService
{
    /**
     * @throws \Throwable
     */
    public function processMessage(Message $message): mixed
    {
        // Retrieve existing assistants and their data
        $assistants = Assistant::select('id', 'name', 'description')->get();
        $assistantDataJson = $assistants->toJson(JSON_PRETTY_PRINT);

        $content = view('prompts.assign-message-to-assistant', [
            'messageContent' => $message->content,
            'assistantsData' => $assistantDataJson,
        ]);

        $content = $content->render();

        // Call OpenAI API to evaluate the message and suggest assistants or roles
        $responseContent = $this->getOpenAIResponse([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
        ]);

        return json_decode($responseContent, true);
    }

    protected function getOpenAIResponse(array $params): string
    {
        $response = OpenAI::chat()->create($params);

        return $response['choices'][0]['message']['content'];
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
