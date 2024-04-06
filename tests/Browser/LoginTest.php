<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('can login', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->withPersonalTeam()->create();
        $browser->visit('/login')
            ->type('#email', $user->email)
            ->type('#password', 'password')->click('button[type="submit"]')
            ->waitForLocation('/dashboard')->assertSee('Dashboard')->screenshot('login');
    });
});

test('can switch to nova', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->withPersonalTeam()->create();
        $browser->loginAs($user)
            ->visit(config('nova.path')
            )->assertSee('Main')->assertSee($user->name)->screenshot('nova');
    });

});
