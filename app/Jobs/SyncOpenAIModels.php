<?php

namespace App\Jobs;

use App\Models\AiModel;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenAI\Laravel\Facades\OpenAI;

class SyncOpenAIModels
{
    use Dispatchable;

    use SerializesModels;


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = OpenAI::models()->list();

        $data = collect($response->toArray()['data']);

        $data->each(function ($model) {
            AiModel::updateOrCreate([
                'name' => $model['id'],
            ], [
                'owned_by' => $model['owned_by'],
            ]);
        });
    }
}
