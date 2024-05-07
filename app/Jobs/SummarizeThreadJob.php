<?php

namespace App\Jobs;

use App\Models\Thread;
use App\Nova\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenAI\Laravel\Facades\OpenAI;

class SummarizeThreadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Thread $thread;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $summary = json_encode([
            'messages' => $this->thread->messages->map(function (Message $message) {
                return [
                    'user' => $message->user->name,
                    'content' => $message->content,
                ];
            }),
        ]);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'provide a markdown summary of this thread from the provided
                json provide collapsible functionality where appropriate please make more concise and provide any context
                about the conversation a summary of names and messages with bullet points could be helpful too'.$summary],
            ],
        ]);

        $this->thread->update(['description' => $response['choices'][0]['message']['content']]);
        $this->thread->save();
    }
}
