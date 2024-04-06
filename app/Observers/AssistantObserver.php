<?php

namespace App\Observers;

use App\Models\Assistant;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Assistants\AssistantResponse;

class AssistantObserver
{
    /**
     * Handle the Assistant "created" event.
     */
    public function created(Assistant $assistant): void
    {
        $response = $this->createOpenAiAssistant($assistant);

    }

    /**
     * Handle the Assistant "updated" event.
     */
    public function updated(Assistant $assistant): void
    {
        OpenAI::assistants()->modify($assistant->provider_value, [
            'name' => $assistant->name,
            'instructions' => $assistant->instructions,
            'model' => $assistant->aiModel->name,
        ]);
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

    public function createOpenAiAssistant(Assistant $assistant): AssistantResponse
    {
        $response = OpenAI::assistants()->create([
            'name' => $assistant->name,
            'instructions' => $assistant->instructions,
            'model' => $assistant->aiModel->name,
        ]);

        $assistant->update([
            'provider_value' => $response['id'],
        ]);

        return $response;
    }
}
