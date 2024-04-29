<?php

namespace App\Observers;

use App\Models\Assistant;
use App\Models\Message;
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
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Evaluate the following assistants for the message '{$message->content}': ".$assistants->toJson().
                        ' Please respond with a JSON structure detailing the point values for each assistant based on their qualifications, '.
                        'reasons for each point allocation, and suggest two new assistants if no current assistant is qualified. Please provide a description for the assistant, as well as overall instructions.',
                ],
            ],
        ]);

        // Assuming the response is well-formed JSON
        $data = json_decode($response['choices'][0]['message']['content'], true);

        dd($data);
        if (isset($data['assistants'])) {
            foreach ($data['assistants'] as $assistant) {
                dd($assistant);
                // Process each qualified assistant
                // Example: You might update some records or trigger further actions
            }
        }

        if (isset($data['suggested_assistants'])) {
            // Handle suggested assistants if no qualified current assistants
            foreach ($data['suggested_assistants'] as $suggestion) {
                $created = Assistant::create(
                    [
                        'name' => $suggestion['name'],
                        'ai_model_id' => 9,
                        'description' => $suggestion['description'],
                        //                    'instructions' => $suggestion['instructions'],
                    ],
                );
                if ($created) {
                    Log::info('Created new assistant: '.$suggestion['name']);
                } else {
                    Log::error('Failed to create new assistant: '.$suggestion['name']);
                }
            }
        }

    }
}
