<?php

// test that nova is installed

use App\Models\User;

test('verify nova directory exists', function () {
    $this->assertTrue(is_dir(base_path('App/Nova')));
});

// test that nova login page is accessible
test('nova path redirects to login', function () {
    $this->get(config('nova.path'))
        ->assertStatus(302)
        ->assertRedirect(config('nova.path').'/login');
});

test('nova dashboard is accessible after login', function () {
    // Assuming you have a user factory and the user has permission to access Nova
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(config('nova.path'))
        ->assertRedirect(config('nova.path').'/dashboards/main');
});
