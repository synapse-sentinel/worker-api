<?php

use App\Models\AiModel;
use App\Models\User;

test('can create with factory', function () {

    $model = AiModel::factory()->create();

    $this->assertModelExists($model);
});

test('nova resource can be viewed', function () {

    $user = User::factory()->create();
    $this->be($user);
    $model = AiModel::factory()->create();
    $response = $this->get('/resources/ai-models/'.$model->id);

    $response->assertStatus(200);
});

test('can be soft deleted', function () {

    $model = AiModel::factory()->create();

    $model->delete();

    $this->assertSoftDeleted($model);
});
