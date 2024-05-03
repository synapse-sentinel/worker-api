<?php

namespace App\Console\Commands;

use App\Models\Thread;
use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

class RetrieveTreads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'treads:retrieve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Thread::all()->each(function (Thread $thread) {
            $this->info('Retrieving thread: '.$thread->name);
            $this->info('Description: '.$thread->description);
            $this->info('Provider value: '.$thread->provider_value);
            $response = OpenAI::threads()->messages()->list($thread->provider_value);
            collect($response->data)->each(function ($message) use ($thread) {

                $existingMessage = $thread->messages()->updateOrCreate([
                    'content' => $message->content[0]->text->value,
                    'role' => $message->role,
                    'user_id' => $this->findAssistant($message),
                    'provider_value' => $message->id,
                    'processed' => true,
                ]);
                $this->info('Message: '.$message->content[0]->text->value);
            });
            $this->info('Messages retrieved: '.count($response->data));
        });
    }

    private function findAssistant($message)
    {
        return \App\Models\Assistant::where('provider_value', $message->assistantId)->first()?->user_id ?? 1;
    }
}
