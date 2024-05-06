<?php

// login route but redirect to novas login

use Illuminate\Support\Facades\Route;

Route::get('/redirect-to-login', function () {
    return redirect('/login');

})->name('login');
