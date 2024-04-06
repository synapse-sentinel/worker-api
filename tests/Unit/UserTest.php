<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('can create a user', function () {
    $user = User::factory()->state(['name' => 'John Doe'])->create();

    expect($user->name)->toBe('John Doe');
});
