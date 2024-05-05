<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

class AiModelsSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai-models:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
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
