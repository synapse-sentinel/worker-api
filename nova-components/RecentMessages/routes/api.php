<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

Route::post('/create-message/{thread}', function (Request $request, App\Models\Thread $thread) {
    $thread->messages()->create([
        'user_id' => Auth::user()->getAuthIdentifier(),
        'content' => $request->input('reply'),
    ]);

    return response()->json(['message' => 'Message created!']);
});
