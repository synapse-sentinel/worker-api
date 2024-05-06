<?php

namespace App\Observers;

use App\Models\Assistant;
use App\Models\User;
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
        $this->createUserForAssistant($assistant, $response);

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

    private function createUserForAssistant(Assistant $assistant, AssistantResponse $response): void
    {
        $user = User::create([
            'name' => $assistant->name,
            'email' => $assistant->name.'@synapse-sentinel.com',
            'password' => bcrypt('password'),
        ]);

        $assistant->update([
            'user_id' => $user->id,
        ]);
    }
}
