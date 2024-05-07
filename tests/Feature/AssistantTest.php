<?php

use App\Models\Assistant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use OpenAI\Laravel\Facades\OpenAI;

test('can be created with factory', function () {
    $model = Assistant::factory()->create();
    $this->assertDatabaseHas('assistants', ['id' => $model->id]);
    $this->assertModelExists($model);
});

test('creates an assistant in OpenAI API when created', function () {
    Sanctum::actingAs(User::factory()->create());

    $assistant = Assistant::factory()->create();

    $this->assertNotNull($assistant->refresh()->provider_value);
});

test('updating an assistant model updates in openai', function () {
    Sanctum::actingAs(User::factory()->create());

    $assistant = Assistant::factory()->create();

    $assistant->update(['name' => 'Updated Assistant']);

    $this->assertEquals('Updated Assistant', $assistant->refresh()->name);

    $openaiAssistant = OpenAI::assistants()->retrieve($assistant->provider_value);
    $this->assertEquals('Updated Assistant', $openaiAssistant['name']);
});

test('can be viewed in nova', function () {
    $this->actingAs(
        User::factory()->create());
    $model = Assistant::factory()->create();
    $response = $this->get(config('nova.path').'resources/assistants/'.$model->id);
    $response->assertStatus(200);
});

it('creates its user when created', function () {
    $assistant = Assistant::factory()->create();
    $this->assertNotNull($assistant->user);
});
