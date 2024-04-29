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

    protected function createThreadForMessage(Message $message)
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

    protected function generateThreadName(Message $message)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => "Suggest a thread name for the message: '{$message->content}'"],
            ],
        ]);

        return $response['choices'][0]['message']['content'];
    }

    protected function generateThreadDescription(Message $message, $threadName)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Describe the thread named '{$threadName}' based on this message: '{$message->content}'",
                ],
            ],
        ]);

        return $response['choices'][0]['message']['content'];
    }

    protected function assignMessageToAssistant(Message $message)
    {
        $assistants = Assistant::select('id', 'name', 'description')->get();
        $assistantDataJson = $assistants->toJson(JSON_PRETTY_PRINT);
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Evaluate the message: '{$message->content}'. Provide a JSON response with two keys, assistants, and suggested".
                        " 'assistants' for those who are highly recommended with detailed points and reasons, and 'suggestions' for new assistant roles needed based on the content.".
                        " Include in 'suggestions' the necessary fields to create these new assistants such as name, description, and instructions.".
                        "\n\nCurrent Assistants Data:\n".$assistantDataJson,
                ],
            ],
        ]);

        // Assuming the response is well-formed JSON
        $data = json_decode($response['choices'][0]['message']['content'], true);

        if (isset($data['assistants'])) {
            foreach ($data['assistants'] as $assistant) {
                try {
                    dump($assistant);
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

        if (isset($data['suggestions'])) {
            foreach ($data['suggestions'] as $suggestion) {
                dump($suggestion);
                $created = Assistant::create([
                    'name' => $suggestion['name'],
                    'description' => $suggestion['description'],
                    'instructions' => $suggestion['instructions'],
                    'ai_model_id' => 9,
                ]);
                if ($created) {
                    Log::info('Created new assistant: '.$suggestion['name']);
                } else {
                    Log::error('Failed to create new assistant: '.$suggestion['name']);
                }
            }
        }
    }
}
