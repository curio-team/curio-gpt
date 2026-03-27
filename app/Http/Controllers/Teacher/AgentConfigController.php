<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AgentConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentConfigController extends Controller
{
    public function index(): View
    {
        $agents = AgentConfig::orderBy('name')->get();

        return view('teacher.agents.index', compact('agents'));
    }

    public function create(): View
    {
        return view('teacher.agents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'instructions' => ['required', 'string', 'max:10000'],
        ]);

        AgentConfig::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('teacher.agents.index')
            ->with('success', 'Agent created successfully.');
    }

    public function edit(AgentConfig $agentConfig): View
    {
        return view('teacher.agents.edit', compact('agentConfig'));
    }

    public function update(Request $request, AgentConfig $agentConfig): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'instructions' => ['required', 'string', 'max:10000'],
        ]);

        $agentConfig->update($validated);

        return redirect()->route('teacher.agents.index')
            ->with('success', 'Agent updated successfully.');
    }

    public function destroy(AgentConfig $agentConfig): RedirectResponse
    {
        $agentConfig->delete();

        return redirect()->route('teacher.agents.index')
            ->with('success', 'Agent deleted.');
    }
}
