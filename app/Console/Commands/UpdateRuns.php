<?php

namespace App\Console\Commands;

use App\Models\Run;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
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
        Run::whereIn('status', ['queued', 'in_progress'])->get()->each(function (Run $run) use (&$completedRuns) {
            $this->info('Checking run: '.$run->id);
            try {
                $response = OpenAI::threads()->runs()->retrieve(threadId: $run->thread->provider_value, runId: $run->provider_value);
                Log::info('Run response: '.json_encode($response));
                $completedRuns = $response['status'] === 'completed';
                if ($response['status'] === 'failed') {
                    dd($run->messageRecommendation()->get());
                }
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
