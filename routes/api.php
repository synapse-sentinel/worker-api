<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\ThreadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('assistants', AssistantController::class);
    Route::apiResource('threads', ThreadController::class);
});
