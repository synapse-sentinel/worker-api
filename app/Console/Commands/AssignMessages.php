<?php

namespace App\Console\Commands;

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
    public function handle(AssistantService $assistantService)
    {
        $messageToAssign = \App\Models\Message::where('processed', false)->get();
        $this->info('Assigning '.$messageToAssign->count().' messages to threads...');
        $messageToAssign->each(function (\App\Models\Message $message) use ($assistantService) {
            $this->info('Assigning message: '.$message->content);
            $this->info('in thread: '.$message->thread->name);
            $response = $assistantService->processMessage($message);
            if (is_array($response) && array_key_exists('potential_assignees', $response)) {
                $potentials = collect($response['potential_assignees'])->each(function ($assistant) use ($message) {
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

            if (is_array($response) && array_key_exists('suggested_assistants', $response) && $response['suggested_assistants']) {

                $this->info('Suggestions: '.implode(', ', $response['suggested_assistants']));
            }
        });

    }
}
