<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $conversations = DB::table('agent_conversations as c')
            ->leftJoin('agent_configs as ac', 'c.agent_config_id', '=', 'ac.id')
            ->leftJoin('users as u', 'c.user_id', '=', 'u.id')
            ->select(
                'c.id',
                'c.title',
                'c.user_id',
                'c.agent_config_id',
                'c.updated_at',
                'ac.name as agent_name',
                'u.name as student_name',
            )
            ->orderByDesc('c.updated_at')
            ->paginate(30);

        return view('teacher.chats.index', compact('conversations'));
    }

    public function show(string $conversationId): View
    {
        $conversation = DB::table('agent_conversations as c')
            ->leftJoin('agent_configs as ac', 'c.agent_config_id', '=', 'ac.id')
            ->leftJoin('users as u', 'c.user_id', '=', 'u.id')
            ->select(
                'c.id',
                'c.title',
                'c.updated_at',
                'ac.name as agent_name',
                'u.name as student_name',
            )
            ->where('c.id', $conversationId)
            ->first();

        abort_if(is_null($conversation), 404);

        $messages = DB::table('agent_conversation_messages')
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get(['role', 'content', 'created_at']);

        return view('teacher.chats.show', compact('conversation', 'messages'));
    }
}
