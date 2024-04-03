<?php

use App\Nova\Actions\SyncModels;
use App\Jobs\SyncOpenAIModels;
use Illuminate\Support\Facades\Queue;

it('dispatches sync open ai models job', function () {

    $action = new SyncModels();
    $action->handle();

    $this->assertDatabaseHas('ai_models', [
        'name' => 'davinci-002',
        'owned_by' => 'system',
    ]);
});
