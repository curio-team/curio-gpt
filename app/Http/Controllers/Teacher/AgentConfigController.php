<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AgentConfig;
use App\Services\SdApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentConfigController extends Controller
{
    public function __construct(
        private readonly SdApiService $sdApiService
    ) {}

    public function index(): View
    {
        $agents = AgentConfig::orderBy('name')->get();

        return view('teacher.agents.index', compact('agents'));
    }

    public function create(): View
    {
        $groups = $this->sdApiService->getGroups();

        return view('teacher.agents.create', compact('groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'instructions' => ['required', 'string', 'max:10000'],
            'allowed_groups' => ['nullable', 'array'],
            'allowed_groups.*' => ['integer'],
        ]);

        AgentConfig::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('teacher.agents.index')
            ->with('success', 'Agent created successfully.');
    }

    public function edit(AgentConfig $agent): View
    {
        $groups = $this->sdApiService->getGroups();

        return view('teacher.agents.edit', compact('agent', 'groups'));
    }

    public function update(Request $request, AgentConfig $agent): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'instructions' => ['required', 'string', 'max:10000'],
            'allowed_groups' => ['nullable', 'array'],
            'allowed_groups.*' => ['integer'],
        ]);

        $agent->update($validated);

        return redirect()->route('teacher.agents.index')
            ->with('success', 'Agent updated successfully.');
    }

    public function destroy(AgentConfig $agent): RedirectResponse
    {
        $agent->delete();

        return redirect()->route('teacher.agents.index')
            ->with('success', 'Agent deleted.');
    }
}
