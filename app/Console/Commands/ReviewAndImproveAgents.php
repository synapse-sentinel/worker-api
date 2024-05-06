<?php

namespace App\Console\Commands;

use App\Models\Assistant;
use App\Models\Thread;
use Illuminate\Console\Command;

class ReviewAndImproveAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:review';

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
        $ceo = Assistant::first();
        $thread = Thread::updateOrCreate(['name' => 'Performance Review', 'description' => 'Review and Improve Agents']);
        Assistant::all()->each(function (Assistant $assistant) use ($thread) {
            $content = view('prompts.assistant-review', ['assistantName' => $assistant->name, 'assistantDescription' => $assistant->description, 'assistantMessages' => $assistant->user->messages()->pluck('content')])->render();
            $thread->messages()->create(['content' => $content, 'assistant_id' => $assistant->id, 'user_id' => 1]);

        });
    }
}
