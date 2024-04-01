<?php

test('can be created with factory', function () {
    $model = \App\Models\Assistant::factory()->create();
    $this->assertDatabaseHas('assistants', $model->toArray());
    $this->assertModelExists($model);
});
