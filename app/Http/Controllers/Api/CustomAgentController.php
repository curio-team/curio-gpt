<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\CustomAgent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomAgentController extends Controller
{
    public function handle(Request $request)
    {
        $agent = (new CustomAgent)->forUser($request->user());

        $conversationId = $request->input('conversationId');

        if ($conversationId) {
            $agent->continue($conversationId, as: $request->user());
        }

        $stream = $agent->stream($request->input('prompt'));

        return response()->stream(function () use ($stream) {
            $conversationId = null;

            $stream->then(function ($response) use (&$conversationId) {
                $conversationId = $response->conversationId;
            });

            foreach ($stream as $event) {
                yield 'data: '.((string) $event)."\n\n";
            }

            if ($conversationId) {
                yield 'data: '.json_encode([
                    'type' => 'conversation_id',
                    'conversation_id' => $conversationId,
                ])."\n\n";
            }

            yield "data: [DONE]\n\n";
        }, headers: ['Content-Type' => 'text/event-stream']);
    }
}
