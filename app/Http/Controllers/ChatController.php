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
            $personalInfo = $this->sdApiService->getPersonalInfo();
            $userGroupIds = collect($personalInfo['groups'] ?? [])->pluck('id')->all();

            $agents = AgentConfig::orderBy('name')
                ->get(['id', 'name', 'description', 'image_path', 'allowed_groups'])
                ->filter(function (AgentConfig $agent) use ($userGroupIds) {
                    if (empty($agent->allowed_groups)) {
                        return false;
                    }

                    return count(array_intersect($agent->allowed_groups, $userGroupIds)) > 0;
                })
                ->values();
        }

        return view('index', compact('agents'));
    }

    public function show(Request $request, AgentConfig $agentConfig): View
    {
        $user = $request->user();

        if (! $user->isTeacher()) {
            $personalInfo = $this->sdApiService->getPersonalInfo();
            $userGroupIds = collect($personalInfo['groups'] ?? [])->pluck('id')->all();

            if (empty($agentConfig->allowed_groups) || count(array_intersect($agentConfig->allowed_groups, $userGroupIds)) === 0) {
                abort(403);
            }
        }

        return view('chat', compact('agentConfig'));
    }
}
