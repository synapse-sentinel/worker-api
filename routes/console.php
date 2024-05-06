<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Artisan::call('ai-models:sync');
})->daily();

Schedule::call(function () {
    Artisan::call('messages:assign');
})->everyMinute();

Schedule::call(function () {
    Artisan::call('messages:process-recommendations');
})->everyFifteenMinutes();

Schedule::call(function () {
    Artisan::call('threads:retrieve');
})->everyMinute();
