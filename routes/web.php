<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\Teacher\AgentConfigController;
use App\Http\Controllers\Teacher\ChatController as TeacherChatController;
use App\Http\Controllers\Teacher\ObservationController as TeacherObservationController;
use App\Http\Controllers\Teacher\UsageController as TeacherUsageController;
use App\Http\Middleware\EnsureUserIsTeacher;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('home');
    Route::get('/chat/{agentConfig}', [ChatController::class, 'show'])->name('chat.show');
});

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

        Route::post('agents/{agent}/attachments', [AgentConfigController::class, 'storeAttachment'])
            ->name('agents.attachments.store');
        Route::delete('agents/{agent}/attachments/{attachmentId}', [AgentConfigController::class, 'destroyAttachment'])
            ->name('agents.attachments.destroy');
        Route::get('agents/{agent}/attachments/{attachmentId}/download', [AgentConfigController::class, 'downloadAttachment'])
            ->name('agents.attachments.download');

        Route::get('chats', [TeacherChatController::class, 'index'])->name('chats.index');
        Route::get('chats/{conversation}', [TeacherChatController::class, 'show'])->name('chats.show');

        Route::get('usage', [TeacherUsageController::class, 'index'])->name('usage.index');

        Route::get('observations', [TeacherObservationController::class, 'index'])->name('observations.index');
        Route::get('observations/{id}', [TeacherObservationController::class, 'show'])->name('observations.show');
    });
