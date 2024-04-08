<?php

use App\Models\Thread;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('can be retrieved over api', function () {

    Sanctum::actingAs(User::factory()->create());

    $model = Thread::factory()->create();
    $response = $this->get('/api/threads/'.$model->id);

    $response->assertStatus(200);

    $response->assertJsonFragment(['name' => $model->name]);
});

it('can be listed over api', function () {

    Sanctum::actingAs(User::factory()->create());

    $model = Thread::factory()->create();
    $response = $this->get('/api/threads');

    $response->assertStatus(200);

    $response->assertJsonFragment([$model->title]);
});

it('can be created over api', function () {

    Sanctum::actingAs(User::factory()->create());

    $response = $this->post('/api/threads',
        [
            'name' => 'Test Thread',
            'description' => 'Test Content',
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'Test Thread'])
        ->assertJsonFragment(['description' => 'Test Content']);
});

test('can by soft deleted over api', function () {

    Sanctum::actingAs(User::factory()->create());

    $model = Thread::factory()->create();

    $this->delete('/api/threads/'.$model->id)
        ->assertStatus(204);

    $this->assertSoftDeleted($model);
});

it('can be updated over api', function () {

    Sanctum::actingAs(User::factory()->create());

    $model = Thread::factory()->create();

    $this->put('/api/threads/'.$model->id,
        [
            'name' => 'Updated Thread',
            'description' => 'Updated Content',
        ])
        ->assertStatus(200)
        ->assertJsonFragment(['name' => 'Updated Thread'])
        ->assertJsonFragment(['description' => 'Updated Content']);
});
