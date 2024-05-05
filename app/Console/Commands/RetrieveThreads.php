<?php

namespace App\Console\Commands;

use App\Models\Assistant;
use App\Models\Thread;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\WithFaker;
use OpenAI\Laravel\Facades\OpenAI;

use function Laravel\Prompts\table;

class RetrieveThreads extends Command
{
    use withFaker;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'threads:retrieve';

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
            table([
                ['Thread', 'Description', 'Provider value'],
                [$thread->name, $thread->description, $thread->provider_value],
            ]);

            $response = OpenAI::threads()->messages()->list($thread->provider_value)->toArray();

            $this->info(' incoming -'.count($response['data']).' messages for thread '.$thread->name);
            collect($response['data'])->each(function ($message) use ($thread) {
                $possibleDupe = $thread->messages()->where('content', $message['content'][0]['text']['value'])->first();
                if ($message['assistant_id'] == null) {
                    $this->info('skipping user messages');

                    return;
                }

                if ($possibleDupe) {
                    $this->info('.Message already exists skipping: ');

                    return;
                }
                $existingMessage = $thread->messages()->updateOrCreate([
                    'content' => $message['content'][0]['text']['value'],
                    'user_id' => $this->findAssistant($message),
                    'role' => 'assistant',
                ]);
            });
        });
    }

    private function findAssistant($message)
    {
        $assistant = Assistant::where('provider_value', $message['assistant_id'])->first();
        $this->info('Assigning message to assistant: '.$assistant->name);
        if ($assistant->user_id == null) {
            $assistant->user()->create([
                'name' => $assistant->name,
                'email' => $assistant->name.'@synapse-sentinel.com',
                'password' => bcrypt('password'),
            ]);
        }

        return $assistant->user_id;
    }
}
