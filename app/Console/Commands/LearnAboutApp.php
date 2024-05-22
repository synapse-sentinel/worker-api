<?php

namespace App\Console\Commands;

use App\Models\Assistant;
use Illuminate\Auth\Events\Login;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

class LearnAboutApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:learn {assistant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows a provided assistant to learn about the app.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $assistant = Assistant::find($this->argument('assistant'));

        if (! $assistant) {
            $this->error('Assistant not found.');

            return;
        }

        $this->info("$assistant->name is learning about the app.");

        Auth::login($assistant->user);

        if (! Auth::check()) {
            $this->error('Failed to log in the assistant.');

            return;
        }

        $this->info('Assistant is now logged in: '.Auth::user()->name);
        event(new Login('web', Auth::user(), false));

        $prompt = 'Learn about the Laravel app you are a part of. Please ask specific questions to understand various aspects of the app, such as controllers, models, routes, and migrations.';
        // Create a new thread for the assistant
        try {

            // Send the system prompt to the thread
            $messageResponse = OpenAI::threads()->createAndRun([
                'assistant_id' => $assistant->provider_value,
                'thread' => [
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ],
            ]);

            $threadId = $messageResponse['thread_id'];

            $messageContent = $messageResponse['content'] ?? 'No response from the assistant';
            $runId = $messageResponse['id'] ?? null;

            $this->info('Run ID: '.$runId);

            $isComplete = false;
            while (! $isComplete) {
                sleep(10); // Wait for 10 seconds before polling again

                $response = OpenAI::threads()->messages()->list($threadId, [
                    'limit' => 10,
                ])->toArray();

                $messageContent = $response['data'][0]['content'][0]['text']['value'] ?? 'none';

                $isComplete = ! ($messageContent === 'none');

                // Call the 'code:find-similar' command

            }
            $files = $this->call('code:find-similar', ['prompt' => $messageContent]);

            dd($files);
        } catch (\Exception $e) {
            $this->error('An error occurred while processing the request: '.$e->getMessage());
        }
    }
}
