<?php

use App\Nova\Actions\SyncModels;

it('dispatches sync open ai models job', function () {

    $action = new SyncModels();
    $action->handle();

    $this->assertDatabaseHas('ai_models', [
        'name' => 'davinci-002',
        'owned_by' => 'system',
    ]);
});
