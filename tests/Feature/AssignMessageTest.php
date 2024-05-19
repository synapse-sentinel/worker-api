<?php

use Illuminate\Support\Facades\Artisan;

it('handles empty message recommendations gracefully', function () {
    Artisan::call('messages:process-recommendations');
    expect(Artisan::output())->toContain('Processing 0 message recommendations...');
});
