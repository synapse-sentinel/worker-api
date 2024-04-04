<?php

namespace App\Observers;

use App\Models\Assistant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        if ($assistant->avatar === null) {
            // generate a prompt to create a cool avatar use the assistant's data to generate a prompt and all other data too
            $prompt = "Create a prompt for dall-e-3 for a  cool avatar for the assistant named {$assistant->name} with the following description: {$assistant->instructions}. Please make sure the avatar is unique and cool and photo-realistic.";

            $promptResponse = OpenAI::chat()->create([
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ], 'model' => 'gpt-4',
            ]);

            $prompt = $promptResponse->choices[0]->message->content;

            $response = OpenAI::images()->create(
                [
                    'prompt' => $prompt,
                    'model' => 'dall-e-3',
                ]);

            // Download the image
            $imageContent = file_get_contents($response->data[0]->url);

            $filename = Str::random(10).'.png';

            // Save the image to the 'public' disk, in the 'avatars' directory
            $stored = Storage::disk('public')->put('avatars/'.$filename, $imageContent);

            if ($stored) {
                // Construct the path of the saved image
                $path = 'avatars/'.$filename;

                // Update the 'avatar' field with the path of the saved image
                $assistant->update([
                    'avatar' => $path,
                ]);
            }
        }
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
