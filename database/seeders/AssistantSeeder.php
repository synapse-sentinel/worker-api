<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\Assistant;
use Illuminate\Database\Seeder;

class AssistantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create lead assistant that will be used to assign messages, and create more assistants
        Assistant::create([
            'name' => 'Lead Assistant',
            'instructions' => 'You are the lead assistant in the worker api laravel app',
            'ai_model_id' => AiModel::where('name', 'gpt-4')->firstOrFail()->id,
        ]);
    }
}
