<?php

namespace App\Observers;

use App\Models\Assistant;
use OpenAI\Laravel\Facades\OpenAI;

class AssistantObserver
{
    /**
     * Handle the Assistant "created" event.
     */
    public function created(Assistant $assistant): void
    {
        $response = OpenAI::assistants()->create([
            'name' => $assistant->name,
            'instructions' => $assistant->instructions,
            'model' => $assistant->aiModel->name,
        ]);

        $assistant->update([
            'provider_value' => $response['id'],
        ]);
    }

    /**
     * Handle the Assistant "updated" event.
     */
    public function updated(Assistant $assistant): void
    {

    }

    /**
     * Handle the Assistant "deleted" event.
     */
    public function deleting(Assistant $assistant): void
    {

        OpenAI::assistants()->delete($assistant->provider_value);
    }

    /**
     * Handle the Assistant "restored" event.
     */
    public function restored(Assistant $assistant): void
    {
        //
    }

    /**
     * Handle the Assistant "force deleted" event.
     */
    public function forceDeleted(Assistant $assistant): void
    {
        //
    }
}
