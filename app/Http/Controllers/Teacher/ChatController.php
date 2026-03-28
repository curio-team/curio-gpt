<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $query = DB::table('agent_conversations as c')
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
            );

        if ($search !== '') {
            $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%';

            // Add a subselect to fetch the first matching message content as a snippet candidate
            $query->selectSub(function ($sub) use ($like) {
                $sub->from('agent_conversation_messages as m')
                    ->select('m.content')
                    ->whereColumn('m.conversation_id', 'c.id')
                    ->where('m.content', 'like', $like)
                    ->orderBy('m.created_at')
                    ->limit(1);
            }, 'match_message');

            // Filter conversations by title or any matching message content
            $query->where(function ($w) use ($like) {
                $w->where('c.title', 'like', $like)
                    ->orWhereExists(function ($sq) use ($like) {
                        $sq->select(DB::raw(1))
                            ->from('agent_conversation_messages as m2')
                            ->whereColumn('m2.conversation_id', 'c.id')
                            ->where('m2.content', 'like', $like);
                    });
            });
        }

        $conversations = $query
            ->orderByDesc('c.updated_at')
            ->paginate(30)
            ->withQueryString();

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
