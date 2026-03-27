<?php

use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\CustomAgentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// The auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/agents', [CustomAgentController::class, 'agents']);
    Route::post('/agent', [CustomAgentController::class, 'handle']);

    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::get('/conversations/{conversationId}/messages', [ConversationController::class, 'messages']);
});
