<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');

Route::get('/login', function () {
    return redirect('/sdclient/redirect');
})->name('login');

Route::get('/sdclient/ready', function () {
    return redirect('/');
});

Route::get('/sdclient/error', function () {
    $error = session('sdclient.error');
    $error_description = session('sdclient.error_description');

    return view('errors.sdclient', compact('error', 'error_description'));
});
