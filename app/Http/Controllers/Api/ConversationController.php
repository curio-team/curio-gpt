<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgentConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'agentConfigId' => ['required', 'string', 'size:36'],
        ]);

        if (! $request->user()->isTeacher()) {
            $agentConfig = AgentConfig::findOrFail($validated['agentConfigId']);

            if (! $agentConfig->isCurrentlyAvailable()) {
                abort(403);
            }

            if ($agentConfig->history_is_disabled) {
                abort(403);
            }
        }

        $query = DB::table('agent_conversations')
            ->where('user_id', $request->user()->id)
            ->where('agent_config_id', $validated['agentConfigId']);

        if (! $request->user()->isTeacher()) {
            $query->whereNull('revoked_at');
        }

        $conversations = $query
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get(['id', 'title', 'updated_at']);

        return response()->json($conversations);
    }

    public function messages(Request $request, string $conversationId): JsonResponse
    {
        $conversationQuery = DB::table('agent_conversations')
            ->where('id', $conversationId)
            ->where('user_id', $request->user()->id);

        if (! $request->user()->isTeacher()) {
            $conversationQuery->whereNull('revoked_at');
        }

        $conversation = $conversationQuery->first();

        abort_if(is_null($conversation), 404);

        if (! $request->user()->isTeacher() && $conversation->agent_config_id !== null) {
            $agentConfig = AgentConfig::find($conversation->agent_config_id);

            if ($agentConfig === null || ! $agentConfig->isCurrentlyAvailable()) {
                abort(403);
            }
        }

        $messages = DB::table('agent_conversation_messages')
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get(['role', 'content']);

        return response()->json($messages);
    }
}
