<?php

namespace App\Console\Commands;

use App\Models\MessageRecommendation;
use App\Models\Run;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessMessageRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:process-recommendations';

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
        $messageRecommendations = MessageRecommendation::where('points', '>', 0)->get();
        $this->info('Processing '.$messageRecommendations->count().' message recommendations...');
        $messageRecommendations->each(function (MessageRecommendation $recommendation) {
            if (! $recommendation->assistant) {
                $this->error('Assistant not found for recommendation: '.$recommendation->id);

                return;
            }
            $this->line("\033[32mProcessing recommendation for message: ".$recommendation->message->content."\033[0m");
            $this->line("\033[34mAssistant: ".$recommendation->assistant->name."\033[0m");
            $this->line("\033[35mReason: ".$recommendation->reason."\033[0m");
            $this->line("\033[36mPoints: ".$recommendation->points."\033[0m");
            $this->info('getting assistant...');

            $assistant = $recommendation->assistant;
            $this->line("\033[32mAssistant: ".$assistant->name."\033[0m");
            $response = $assistant->processMessage($recommendation->message);
            $run = $this->storeRun($recommendation, $response);
            $this->info('Run stored with id: '.$run->id);
            $recommendation->delete();

            Artisan::call(RetrieveThreads::class);

        });
    }

    private function storeRun($recommendation, $response)
    {
        return Run::create([
            'provider_value' => $response['id'],
            'status' => $response['status'],
            'assistant_id' => $recommendation->assistant_id,
            'thread_id' => $recommendation->message->thread_id,
        ]);
    }
}
