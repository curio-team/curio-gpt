<?php

namespace App\Http\Controllers;

use App\Models\AgentConfig;
use App\Services\SdApiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(private readonly SdApiService $sdApiService) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isTeacher()) {
            $agents = AgentConfig::orderBy('name')->get(['id', 'name', 'description', 'image_path']);
        } else {
            $userGroupIds = $this->sdApiService->getPersonalGroupIds();

            $agents = AgentConfig::orderBy('name')
                ->get(['id', 'name', 'description', 'image_path', 'allowed_groups'])
                ->filter(fn (AgentConfig $agent) => $agent->isAllowedForGroups($userGroupIds))
                ->values();
        }

        return view('index', compact('agents'));
    }

    public function show(Request $request, AgentConfig $agentConfig): View
    {
        $user = $request->user();

        if (! $user->isTeacher()) {
            if (! $agentConfig->isAllowedForGroups($this->sdApiService->getPersonalGroupIds())) {
                abort(403);
            }
        }

        return view('chat', compact('agentConfig'));
    }
}
