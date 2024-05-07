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

it('creates a  user for it to use', function () {
    // Assuming you have set up database migrations and model factories
    $assistant = Assistant::factory()->create([
        'name' => 'Test Assistant',
    ]);

    // Perform the action
    $this->post('/assistant', $assistant->toArray());

    // Assert a new user is created
    $this->assertDatabaseHas('users', [
        'email' => 'Test Assistant@synapse-sentinel.com',
    ]);

    // Assert the user is associated with the assistant
    $user = User::where('email', 'Test Assistant@synapse-sentinel.com')->first();
    $this->assertEquals($user->id, $assistant->fresh()->user_id);
});
