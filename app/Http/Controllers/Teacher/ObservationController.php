<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ObservationController extends Controller
{
    public function index(): View
    {
        $observations = DB::table('agent_observations as o')
            ->leftJoin('agent_configs as ac', 'o.agent_config_id', '=', 'ac.id')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
            ->select(
                'o.id',
                'o.category',
                'o.content',
                'o.conversation_id',
                'o.created_at',
                'ac.name as agent_name',
                'u.name as student_name',
            )
            ->orderByDesc('o.created_at')
            ->paginate(30);

        return view('teacher.observations.index', compact('observations'));
    }

    public function show(string $id): View
    {
        $observation = DB::table('agent_observations as o')
            ->leftJoin('agent_configs as ac', 'o.agent_config_id', '=', 'ac.id')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
            ->select(
                'o.id',
                'o.category',
                'o.content',
                'o.conversation_id',
                'o.created_at',
                'ac.name as agent_name',
                'u.name as student_name',
            )
            ->where('o.id', $id)
            ->first();

        abort_if(is_null($observation), 404);

        return view('teacher.observations.show', compact('observation'));
    }
}
