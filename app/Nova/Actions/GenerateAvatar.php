<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Images\CreateResponse;

class GenerateAvatar extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     */
    public function handle(ActionFields $fields, Collection $models): void
    {
        $models->each(function ($model) use ($fields) {

            $image = $this->retrieveImage($fields->get('prompt'));
            $this->storeImage($image, $model);

            return Action::message('The avatar has been generated and stored.');
        });

    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        $prompt = $this->generatePrompt($request->findModelOrFail());

        return [
            Textarea::make('Prompt', 'prompt')->default($prompt),
        ];
    }

    private function generatePrompt($model): string
    {
        $assistant = $model;

        $starter = "Create a prompt for dall-e-3 for a  cool avatar for the assistant named
        {$assistant->name} with the following description: {$assistant->instructions}.
        Please make sure the avatar is unique and cool and photo-realistic.
        Please do not include any text in the image.
        The image should be a square image with a resolution of 512x512 pixels.";

        $response = OpenAI::chat()->create([
            'messages' => [
                ['role' => 'user', 'content' => $starter],
            ], 'model' => 'gpt-4',
        ]);

        return $response->choices[0]->message->content;
    }

    /**
     * Retrieve the image from the OpenAI API.
     */
    private function retrieveImage($prompt): CreateResponse
    {
        return OpenAI::images()->create([
            'prompt' => $prompt,
            'model' => 'dall-e-3',
        ]);
    }

    /**
     * Store the image in the storage.
     */
    private function storeImage($image, $model): void
    {
        $imageContent = file_get_contents($image->data[0]->url);

        $filename = Str::random(10).'.png';

        $stored = Storage::disk('public')->put('avatars/'.$filename, $imageContent);

        $path = 'avatars/'.$filename;
        $model->update([
            'avatar' => $path,
        ]);

    }
}
