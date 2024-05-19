<?php

namespace App\Observers;

use App\Models\Message;
use Illuminate\Support\Facades\Artisan;
use OpenAI\Laravel\Facades\OpenAI;

class MessageObserver
{
    const ASSIGNED_POINTS = 10;

    /**
     * Handle the "created" event for the Message model.
     */
    public function creating(Message $message): void
    {
        $this->createThread($message);
        $this->handleOpenAiIntegration($message);
    }

    public function created(Message $message): void
    {
        Artisan::call('messages:assign');
        $message->thread->summarize();
    }

    /**
     * Manages integration with OpenAI for a given message.
     */
    private function handleOpenAiIntegration(Message $message): void
    {
        $openAiInfo = ['content' => $message->content, 'role' => 'user'];
        $thread = $message->thread;
        if ($thread->provider_value === null) {
            $openAiResponse = OpenAI::threads()->create(['metadata' => ['name' => $thread->name]]);
            $thread->provider_value = $openAiResponse->id;
            $thread->save();
        }

        $openAiResponse = OpenAI::threads()->messages()->create($thread->provider_value, $openAiInfo);
        $message->provider_value = $openAiResponse->id;

    }

    private function createThread(Message $message)
    {
        // assign a message to a thread or create a new thread

        // don't create a thread if the message already belongs to a thread
        if ($message->thread_id) {
            return;
        }

        $query = OpenAI::chat()->create(
            [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system', 'content' => 'Given the following message from the user please return a json
                    object with the following keys: threadName, threadDescription, contexts.
                    The threadName should be a string that is the name of the thread. The description should be summary of the thread thus far context should be any categories from the users message.',
                    ],
                    ['role' => 'user', 'content' => $message->content],
                ],
            ]);

        dd($query->choices[0]->message->content);
        $message->thread()->create([
            'name' => $query->threadName,
            'description' => $query->threadDescription,
            'user_id' => $message->user_id,
        ]);

        \Laravel\Prompts\info('Thread created successfully'.$message->thread->name.'context" '.$query->contexts);
    }
}
