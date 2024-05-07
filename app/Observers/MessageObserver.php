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

        $openAiResponse = OpenAI::threads()->messages()->create($thread->provider_value, $openAiInfo);
        $message->provider_value = $openAiResponse->id;

    }
}
