<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\CustomAgent;
use App\Http\Controllers\Controller;
use App\Models\AgentConfig;
use App\Services\SdApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomAgentController extends Controller
{
    public function __construct(private readonly SdApiService $sdApiService) {}

    public function agents(Request $request): JsonResponse
    {
        if ($request->user()->isTeacher()) {
            $agents = AgentConfig::orderBy('name')
                ->get(['id', 'name'])
                ->map(fn(AgentConfig $agent) => ['id' => $agent->id, 'name' => $agent->name]);

            return response()->json($agents);
        }

        $personalInfo = $this->sdApiService->getPersonalInfo();
        $userGroupIds = collect($personalInfo['groups'] ?? [])->pluck('id')->all();

        $agents = AgentConfig::orderBy('name')
            ->get(['id', 'name', 'allowed_groups'])
            ->filter(function (AgentConfig $agent) use ($userGroupIds) {
                $allowedGroups = $agent->allowed_groups;

                if (empty($allowedGroups)) {
                    return false;
                }

                return count(array_intersect($allowedGroups, $userGroupIds)) > 0;
            })
            ->values()
            ->map(fn(AgentConfig $agent) => ['id' => $agent->id, 'name' => $agent->name]);

        return response()->json($agents);
    }

    public function handle(Request $request)
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:10000'],
            'agentConfigId' => ['required', 'string', 'size:36'],
            'conversationId' => ['nullable', 'string', 'size:36'],
            'branchFrom' => ['nullable', 'array'],
            'branchFrom.conversationId' => ['required_with:branchFrom', 'string', 'size:36'],
            'branchFrom.keepMessageCount' => ['required_with:branchFrom', 'integer', 'min:0'],
        ]);

        $agentConfig = AgentConfig::findOrFail($validated['agentConfigId']);

        if (! $request->user()->isTeacher()) {
            $personalInfo = $this->sdApiService->getPersonalInfo();
            $userGroupIds = collect($personalInfo['groups'] ?? [])->pluck('id')->all();

            if (empty($agentConfig->allowed_groups) || count(array_intersect($agentConfig->allowed_groups, $userGroupIds)) === 0) {
                abort(403);
            }
        }

        $agent = (new CustomAgent($agentConfig->instructions))->forUser($request->user());

        if (isset($validated['branchFrom'])) {
            $targetConversationId = $this->branchConversation(
                $request->user(),
                $validated['branchFrom']['conversationId'],
                $validated['branchFrom']['keepMessageCount'],
                $agentConfig->id,
            );

            $agent->continue($targetConversationId, as: $request->user());
        } elseif ($validated['conversationId'] ?? null) {
            $agent->continue($validated['conversationId'], as: $request->user());
        }

        $agentConfigId = $agentConfig->id;
        $stream = $agent->stream($validated['prompt']);

        return response()->stream(function () use ($stream, $agentConfigId) {
            $conversationId = null;

            $stream->then(function ($response) use (&$conversationId, $agentConfigId) {
                $conversationId = $response->conversationId;

                DB::table('agent_conversations')
                    ->where('id', $conversationId)
                    ->whereNull('agent_config_id')
                    ->update(['agent_config_id' => $agentConfigId]);
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
    private function branchConversation(object $user, string $sourceConversationId, int $keepMessageCount, string $agentConfigId): string
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
            'agent_config_id' => $agentConfigId,
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
