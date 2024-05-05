<?php

use App\Models\User;

test('verify nova directory exists', function () {
    $this->assertTrue(is_dir(base_path('app/Nova')));
});

test('nova path redirects to login', function () {
    $this->get(config('nova.path'))
        ->assertStatus(302)
        ->assertRedirect(config('nova.path').'login');
});

test('nova dashboard is accessible after login', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(config('nova.path'))
        ->assertRedirect(config('nova.path').'dashboards/main');
});
