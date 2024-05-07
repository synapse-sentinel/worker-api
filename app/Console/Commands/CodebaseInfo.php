<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

class CodebaseInfo extends Command
{
    protected $signature = 'codebase:info';

    protected $description = 'Gather codebase information for AI prompt';

    public function handle()
    {

        $structure = [
            'models' => glob('app/Models/*.php'),
            'commands' => glob('app/Console/Commands/*.php'),
            'controllers' => glob('app/Http/Controllers/*.php'),
            'migrations' => glob('database/migrations/*.php'),
            'seeds' => glob('database/seeders/*.php'),
            'factories' => glob('database/factories/*.php'),
        ];

        $prompt = $this->generatePrompt($structure);

        $this->info($prompt);

    }

    private function generatePrompt($structure): string
    {
        $prompt = 'Describe the laravel app, talke about how the work api likely functions Codebase information: ';
        foreach ($structure as $key => $files) {
            $prompt .= $key.': '.count($files).' files, ';
        }
        $prompt .= 'Total files: '.array_sum(array_map('count', $structure));
        // list files now
        foreach ($structure as $key => $files) {
            $prompt .= "\n".$key.' files: '.implode(', ', $files);
        }

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
            ],
        ]);

        return $response['choices'][0]['message']['content'];
    }

    private function sendToAIModelAndGetPrompt($data)
    {
        // Code to send data to AI model via API or library
        // Receive AI-generated prompt as a response
        return 'AI model prompt based on data: '.$data;
    }
}
