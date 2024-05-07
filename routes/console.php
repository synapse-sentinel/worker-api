<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Artisan::call('ai-models:sync');
})->daily();

Schedule::call(function () {
    Artisan::call('messages:process-recommendations');
})->everyMinute();

Schedule::call(function () {
    Artisan::call('threads:retrieve');
})->everyMinute();

Schedule::call(function () {
    Artisan::call('runs:update');
})->everyMinute();

Schedule::call(function () {
    Artisan::call('agent:reflect');
})->hourly();
