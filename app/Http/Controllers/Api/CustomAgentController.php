<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\CustomAgent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomAgentController extends Controller
{
    public function handle(Request $request)
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:10000'],
            'conversationId' => ['nullable', 'string', 'size:36'],
            'branchFrom' => ['nullable', 'array'],
            'branchFrom.conversationId' => ['required_with:branchFrom', 'string', 'size:36'],
            'branchFrom.keepMessageCount' => ['required_with:branchFrom', 'integer', 'min:0'],
        ]);

        $agent = (new CustomAgent)->forUser($request->user());

        if (isset($validated['branchFrom'])) {
            $targetConversationId = $this->branchConversation(
                $request->user(),
                $validated['branchFrom']['conversationId'],
                $validated['branchFrom']['keepMessageCount'],
            );

            $agent->continue($targetConversationId, as: $request->user());
        } elseif ($validated['conversationId'] ?? null) {
            $agent->continue($validated['conversationId'], as: $request->user());
        }

        $stream = $agent->stream($validated['prompt']);

        return response()->stream(function () use ($stream) {
            $conversationId = null;

            $stream->then(function ($response) use (&$conversationId) {
                $conversationId = $response->conversationId;
            });

            foreach ($stream as $event) {
                yield 'data: ' . ((string) $event) . "\n\n";
            }

            if ($conversationId) {
                yield 'data: ' . json_encode([
                    'type' => 'conversation_id',
                    'conversation_id' => $conversationId,
                ]) . "\n\n";
            }

            yield "data: [DONE]\n\n";
        }, headers: ['Content-Type' => 'text/event-stream']);
    }

    /**
     * Create a new conversation branched from an existing one, copying the
     * first $keepMessageCount messages. Returns the new conversation ID.
     */
    private function branchConversation(object $user, string $sourceConversationId, int $keepMessageCount): string
    {
        $sourceConversation = DB::table('agent_conversations')
            ->where('id', $sourceConversationId)
            ->where('user_id', $user->id)
            ->first();

        abort_if(is_null($sourceConversation), 403);

        $messages = DB::table('agent_conversation_messages')
            ->where('conversation_id', $sourceConversationId)
            ->orderBy('id')
            ->limit($keepMessageCount)
            ->get();

        $newConversationId = (string) Str::uuid7();

        DB::table('agent_conversations')->insert([
            'id' => $newConversationId,
            'user_id' => $user->id,
            'title' => $sourceConversation->title,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($messages as $message) {
            DB::table('agent_conversation_messages')->insert([
                'id' => (string) Str::uuid7(),
                'conversation_id' => $newConversationId,
                'user_id' => $message->user_id,
                'agent' => $message->agent,
                'role' => $message->role,
                'content' => $message->content,
                'attachments' => $message->attachments,
                'tool_calls' => $message->tool_calls,
                'tool_results' => $message->tool_results,
                'usage' => $message->usage,
                'meta' => $message->meta,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
            ]);
        }

        return $newConversationId;
    }
}
