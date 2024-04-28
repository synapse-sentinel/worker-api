<?php

use App\Models\Message;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->be($this->user);
});

it('message resource returns valid response', function () {
    $response = $this->get(config('nova.path').'/resources/messages');

    $response->assertStatus(200);
});

it('can be viewed in nova', function () {

    $message = Message::factory()->create();
    $response = $this->get(config('nova.path').'/resources/messages/'.$message->id);

    $response->assertStatus(200);
});
