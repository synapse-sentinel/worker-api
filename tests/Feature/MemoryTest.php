<?php

use App\Models\Memory;

it('can be created', function () {
    $memory = Memory::factory()->create();
    $this->assertDatabaseHas('memories', ['id' => $memory->id]);
});
