<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $messageRecommendations = \App\Models\MessageRecommendation::where('points', '>', 0)->get();
        $this->info('Processing '.$messageRecommendations->count().' message recommendations...');
        $messageRecommendations->each(function (\App\Models\MessageRecommendation $recommendation) {
            $this->line("\033[32mProcessing recommendation for message: ".$recommendation->message->content."\033[0m");
            $this->line("\033[34mAssistant: ".$recommendation->assistant->name."\033[0m");
            $this->line("\033[35mReason: ".$recommendation->reason."\033[0m");
            $this->line("\033[36mPoints: ".$recommendation->points."\033[0m");
            $this->info('getting assistant...');

            $assistant = $recommendation->assistant;
            $this->line("\033[32mAssistant: ".$assistant->name."\033[0m");
            $response = $assistant->processMessage($recommendation->message);
            dd($response);

        });
    }
}
