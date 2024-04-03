<?php

use App\Models\Assistant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('can be created with factory', function () {
    $model = Assistant::factory()->create();
    $this->assertDatabaseHas('assistants', $model->toArray());
    $this->assertModelExists($model);
});

test('can list over api', function () {
    Sanctum::actingAs(
        User::factory()->create());

    $model    = Assistant::factory()->create();
    $response = $this->get('/api/assistants');
    $response->assertStatus(200);
    $response->assertJsonFragment($model->toArray());
});

test('can retrieve over api', function () {
    Sanctum::actingAs(
        User::factory()->create());

    $model    = Assistant::factory()->create();
    $response = $this->get('/api/assistants/' . $model->id);
    $response->assertStatus(200);
    $response->assertJsonFragment($model->toArray());
});

test('can be created over api', function () {
    Sanctum::actingAs(
        User::factory()->create());
   $response =  $this->post('/api/assistants', ['name' => 'Test Assistant', 'instructions' => 'Test Instructions', 'ai_model_id' => \App\Models\AiModel::factory()->create()->id]);
   $response->assertStatus(201)
         ->assertJsonFragment(['name' => 'Test Assistant'])->assertJsonFragment(['instructions' => 'Test Instructions']);
});

test('can by soft deleted over api', function () {
    Sanctum::actingAs(
        User::factory()->create());
    $model = Assistant::factory()->create();
    $this->delete('/api/assistants/' . $model->id)
         ->assertStatus(204);
    $this->assertSoftDeleted($model);
});

test('can be updated over api', function () {
    Sanctum::actingAs(
        User::factory()->create());
    $model = Assistant::factory()->create();
    $this->put('/api/assistants/' . $model->id, ['name' => 'Updated Assistant'])
         ->assertStatus(200)
         ->assertJsonFragment(['name' => 'Updated Assistant']);

});

test('can be viewed in nova', function () {
    $this->actingAs(
        User::factory()->create());
    $model = Assistant::factory()->create();
    $response = $this->get('/nova/resources/assistants/' . $model->id);
    $response->assertStatus(200);
});




