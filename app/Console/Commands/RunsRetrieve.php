<?php

namespace App\Console\Commands;

use App\Models\Run;
use App\Models\User;
use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

class RunsRetrieve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runs:retrieve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'retrieve runs and check status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Run::query()->each(function (Run $run) {
            $this->line("\033[32mChecking run: ".$run->id."\033[0m");
            $this->line("\033[34mProvider value: ".$run->provider_value."\033[0m");
            $this->line("\033[35mStatus: ".$run->status."\033[0m");
            $this->info('checking status...');
            $response = OpenAI::threads()->messages()->list($run->thread->provider_value);
            $assistant = $run->assistant;
            $run->delete();
            if (! $assistant->user) {
                $assistant->user()->associate(User::factory()->state(['name' => $assistant->name])->create());
            }
            $run->thread->messages()->updateOrCreate(
                [
                    'content' => $response->data[0]->content[0]->text->value,
                    'role' => $response->data[0]->role,
                    'user_id' => $assistant->user_id,
                    'provider_value' => $response->data[0]->id,
                    'processed' => true,
                ]);
        });
    }
}
