<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('can view Threads in nova', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::factory()->create(), 'web');
        $browser->visit('/nova/resources/threads')
            ->assertSee('Threads');
    });
});

test('can create threads in nova', function () {
    $this->browse(function (Browser $browser) {
        $testUser = User::factory()->create();
        $browser->loginAs($testUser, 'web');
        $browser->visit('/nova/resources/threads/new')
            ->type('@name', 'Thread Name')
            ->type('@description', 'Thread Description')
            ->click('@create-button');
        $this->assertDatabaseHas('threads', [
            'name' => 'Thread Name',
            'description' => 'Thread Description',
        ]);

        $browser->logout();
        $testUser->forceDelete();
        \App\Models\Thread::where('name', 'Thread Name')->forceDelete();
    });
});
