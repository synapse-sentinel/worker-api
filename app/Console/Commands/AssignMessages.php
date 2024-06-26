<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Services\AssistantService;
use Illuminate\Console\Command;

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
    public function handle(AssistantService $assistantService): void
    {
        $messageToAssign = Message::where('processed', false)->limit(10)->get();
        $this->info('Assigning '.$messageToAssign->count().' messages to Assistants...');
        $messageToAssign->each(function (Message $message) use ($assistantService) {

            $response = $assistantService->processMessage($message);
            $this->info('Response: '.json_encode($response));
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
    }
}
