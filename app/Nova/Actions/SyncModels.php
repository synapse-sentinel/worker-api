<?php

namespace App\Nova\Actions;

use App\Jobs\SyncOpenAIModels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Http\Requests\NovaRequest;

class SyncModels extends Action implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * Perform the action on the given models.
     *
     * @return void
     */
    public function handle(): void
    {
        SyncOpenAIModels::dispatchSync();
    }

    /**
     * Get the fields available on the action.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [];
    }
}
