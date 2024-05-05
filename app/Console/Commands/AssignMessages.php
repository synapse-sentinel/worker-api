<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Services\AssistantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\table;

class AssignMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(AssistantService $assistantService)
    {
        $messageToAssign = Message::where('processed', false)->limit(10)->get();
        $this->info('Assigning '.$messageToAssign->count().' messages to Assistants...');
        $messageToAssign->each(function (Message $message) use ($assistantService) {

            table([
                ['Message', 'User', 'Thread'],
                [$message->content, $message->user->name, $message->thread->name],
            ]);
            $response = $assistantService->processMessage($message);
            if (is_array($response) && array_key_exists('potential_assignees', $response) && $response['potential_assignees']) {

                collect($response['potential_assignees'])->each(function ($assistant) use ($message) {
                    $this->info('Potential assignee: '.$assistant['name']);
                    $this->info('Reason: '.$assistant['reason']);
                    $message->messageRecommendations()->create([
                        'assistant_id' => $assistant['id'],
                        'reason' => $assistant['reason'],
                        'points' => $assistant['points'] ?? 4,
                    ]);

                    $message->update(['processed' => true]);
                });
            }
            collect($response['potential_assignees'] ?? null)->each(function ($assistant) use ($message) {
                $this->info('Potential assignee: '.$assistant['name']);
                $this->info('Reason: '.$assistant['reason']);
                $message->messageRecommendations()->create([
                    'assistant_id' => $assistant['id'],
                    'reason' => $assistant['reason'],
                    'points' => $assistant['points'] ?? 4,
                ]);

                $message->update(['processed' => true]);
            });

            if (is_array($response) && array_key_exists('suggested_assistants', $response) && $response['suggested_assistants']) {

                $this->info('Suggestions: '.implode(', ', $response['suggested_assistants']));
            }
        });

        Artisan::call(ProcessMessageRecommendations::class);

    }
}
