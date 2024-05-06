<?php

namespace App\Observers;

use App\Models\Message;
use OpenAI\Laravel\Facades\OpenAI;

class MessageObserver
{
    const ASSIGNED_POINTS = 10;

    /**
     * Handle the "created" event for the Message model.
     */
    public function created(Message $message): void
    {
        $this->handleOpenAiIntegration($message);
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

        $message->save();
    }

    /**
     * Assign recommendations based on the assistant's input.
     */
    private function assignRecommendations(Message $message): void
    {
        $message->messageRecommendations()->create([
            'assistant_id' => $message->assistant->id,
            'reason' => 'Assigned by User',
            'points' => self::ASSIGNED_POINTS, // Define this constant at the top of the class
        ]);
    }
}
