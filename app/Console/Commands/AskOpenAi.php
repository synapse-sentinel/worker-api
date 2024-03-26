<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use JakubOnderka\PhpConsoleHighlighter\Highlighter;
use OpenAI\Laravel\Facades\OpenAI;

use Symfony\Component\Console\Helper\Table;
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
        $question = text('What is your question?');

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
        preg_match_all('/```(?:[a-z]+\n)?(.*?)```/s', $content, $matches, PREG_SET_ORDER);

        $consoleColor = new ConsoleColor();
        $highlighter = new Highlighter($consoleColor);

        foreach ($matches as $match) {
            $highlightedCode = $highlighter->getWholeFile($match[1]);
            $content = str_replace($match[0], $highlightedCode, $content);
        }

        $this->line($content);
    }

}
