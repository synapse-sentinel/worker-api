<?php

use App\Models\User;

use function Pest\Laravel\be;

test('factory can create model', function () {

    $model = \App\Models\AiModel::factory()->create();

    \Pest\Laravel\assertModelExists($model);
});

test('factory can create model with custom values', function () {

    $model = \App\Models\AiModel::factory()->create([
        'name' => 'Custom Name',
        'owned_by' => 'Custom Owner',
    ]);

    \Pest\Laravel\assertDatabaseHas('ai_models', [
        'name' => 'Custom Name',
        'owned_by' => 'Custom Owner',
    ]);
});

test('nova resource can be viewed', function () {

    $user = User::factory()->create();
    be($user);
    $model = \App\Models\AiModel::factory()->create();

    $response = \Pest\Laravel\get('/nova/resources/ai-models/'.$model->id);

    $response->assertStatus(200);
});

test('can be soft deleted', function () {

    $model = \App\Models\AiModel::factory()->create();

    $model->delete();

    \Pest\Laravel\assertSoftDeleted($model);
});
