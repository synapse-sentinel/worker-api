<?php

namespace App\Observers;

use App\Models\Message;
use OpenAI\Laravel\Facades\OpenAI;

class MessageObserver
{
    public function created(Message $message): void
    {
        $array = ['content' => $message->content, 'role' => $message->role ?? 'user'];
        $thread = $message->thread;
        if ($thread->provider_value == null) {
            $openAi = OpenAI::threads()->create(['metadata' => ['name' => $thread->name, 'description' => $thread->description]]);
            $thread->provider_value = $openAi->id;
            $thread->save();

        }
        $openAi = OpenAI::threads()->messages()->create($thread->provider_value, $array);
        $message->provider_value = $openAi->id;

        if ($message->assistant) {
            $message->messageRecommendations()->create([
                'assistant_id' => $message->assistant->id,
                'reason' => 'Assigned by User',
                'points' => 4,
            ]);
        }
        $message->save();

    }
}
