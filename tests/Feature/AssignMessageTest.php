<?php

use App\Models\Assistant;
use App\Models\Message;
use App\Models\MessageRecommendation;
use Illuminate\Support\Facades\Artisan;

it('handles empty message recommendations gracefully', function () {
    Artisan::call('messages:process-recommendations');
    expect(Artisan::output())->toContain('Processing 0 message recommendations...');
});

it('logs correct details during processing', function () {
    MessageRecommendation::factory()
        ->for(Assistant::factory())
        ->for(Message::factory()->state(['content' => 'Test message']))
        ->create();

    Artisan::call('messages:process-recommendations');

    expect(Artisan::output())->toContain('Processing 1 message recommendations...')
        ->toContain('Processing recommendation for message: Test message');
});
