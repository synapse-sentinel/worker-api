<?php

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

test('can create  with  factory', function () {
    $thread = Thread::factory()->create();
    $this->assertDatabaseHas('threads', ['id' => $thread->id]);
    $this->assertModelExists($thread);
});

test('nova resource can be viewed', function () {
    $user = User::factory()->create();
    $this->be($user);
    $thread = Thread::factory()->create();
    $response = $this->get(config('nova.path').'/resources/threads/'.$thread->id);
    $response->assertStatus(200);
});

test('can be soft deleted', function () {
    $thread = Thread::factory()->create();
    $thread->delete();
    $this->assertSoftDeleted($thread);
});
