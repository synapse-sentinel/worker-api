<?php

namespace App\Console\Commands;

use Highlight\Highlighter;
use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Termwind\render;

class AskOpenAi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ask:open-ai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(): void
    {

        // multi-line html...
        render(<<<'HTML'
    <div>
        <div class="px-1 bg-blue-300 text-green-600">Ask Open AI</div>
        <em class="ml-1">
        I think code highlighting actually works too! Let's try it out.
        </em>
    </div>
HTML
        );
        $question = text('What is your question?');

        //list all models /
         $models = OpenAI::Models()->list();

        dd($models);

        $result = spin(
            fn() => OpenAI::chat()->create([
                'model'    => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $question],
                ],
            ]));

        $content = $result['choices'][0]['message']['content'];
        $this->formatResponse($content);
    }

    protected function formatResponse(string $content): void
    {
        dd($content);
    }

}
