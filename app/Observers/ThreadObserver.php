<?php

namespace App\Observers;

use App\Models\Thread;
use OpenAI\Laravel\Facades\OpenAI;

class ThreadObserver
{
    /**
     * Handle the Thread "created" event.
     */
    public function created(Thread $thread): void
    {
        // create in open ai and persist provider_value
        $openAi = OpenAI::threads()->create(['metadata' => ['name' => $thread->name, 'description' => $thread->description]]);
        $thread->provider_value = $openAi->id;
        $thread->save();
    }

    /**
     * Handle the Thread "updated" event.
     */
    public function updated(Thread $thread): void
    {
        //
    }

    /**
     * Handle the Thread "deleted" event.
     */
    public function deleted(Thread $thread): void
    {
        //
    }

    /**
     * Handle the Thread "restored" event.
     */
    public function restored(Thread $thread): void
    {
        //
    }

    /**
     * Handle the Thread "force deleted" event.
     */
    public function forceDeleted(Thread $thread): void
    {
        //
    }
}
