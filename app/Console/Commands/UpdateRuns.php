<?php

namespace App\Console\Commands;

use App\Models\Run;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use OpenAI\Laravel\Facades\OpenAI;

class UpdateRuns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runs:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'checks up on queued runs and updates their status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking up on runs...');
        $completedRuns = false;
        Run::where('status', 'queued')->get()->each(function (Run $run) {
            $this->info('Checking run: '.$run->id);
            try {
                $response = OpenAI::threads()->runs()->retrieve(threadId: $run->thread->provider_value, runId: $run->provider_value);
                $completedRuns = $response['status'] === 'completed';
                $run->update([
                    'status' => $response['status'],
                ]);
            } catch (\Exception $e) {
                $this->error('Error updating run: '.$run->id);
                if (str_contains($e->getMessage(), 'No run found')) {
                    $this->error('Deleting run: '.$run->id.' as it was not found in OpenAI');
                    $run->update([
                        'status' => 'deleted',
                    ]);
                }
            }
        });

        if ($completedRuns) {
            Artisan::call('threads:retrieve');
        }
    }
}
