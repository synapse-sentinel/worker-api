<?php

namespace App\Console\Commands;

use App\Models\Assistant;
use Illuminate\Console\Command;

class AgentReflect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:reflect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows the agent to reflect and iterate its instructions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Assistant::each(function (Assistant $assistant) {
            $assistant->reflect();
        });
    }
}
