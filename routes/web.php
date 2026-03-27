<?php

use App\Http\Controllers\Teacher\AgentConfigController;
use App\Http\Controllers\Teacher\ChatController;
use App\Http\Middleware\EnsureUserIsTeacher;
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

Route::middleware(['auth', EnsureUserIsTeacher::class])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::resource('agents', AgentConfigController::class)
            ->except(['show'])
            ->names('agents');

        Route::get('chats', [ChatController::class, 'index'])->name('chats.index');
        Route::get('chats/{conversation}', [ChatController::class, 'show'])->name('chats.show');
    });
