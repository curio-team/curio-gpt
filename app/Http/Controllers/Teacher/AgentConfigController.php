<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AgentConfig;
use App\Services\ModelPricingService;
use App\Services\OpenAiModelService;
use App\Services\SdApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AgentConfigController extends Controller
{
    public function __construct(
        private readonly SdApiService $sdApiService,
        private readonly OpenAiModelService $openAiModels,
        private readonly ModelPricingService $pricing,
    ) {}

    public function index(): View
    {
        $agents = AgentConfig::orderBy('name')->get();
        $groups = collect($this->sdApiService->getGroups())->keyBy('id');

        return view('teacher.agents.index', compact('agents', 'groups'));
    }

    public function create(): View
    {
        $groups = $this->sdApiService->getGroups();
        $modelIds = $this->openAiModels->listChatModels();
        $models = $this->mapAndSortModelsWithPricing($modelIds);

        return view('teacher.agents.create', compact('groups', 'models'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:300'],
            'instructions' => ['required', 'string', 'max:10000'],
            'allowed_groups' => ['nullable', 'array'],
            'allowed_groups.*' => ['integer'],
            'allowed_models' => ['nullable', 'array'],
            'allowed_models.*' => ['string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_enabled' => ['required', 'boolean'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_until' => ['nullable', 'date_format:H:i'],
            'monitoring_is_enabled' => ['required', 'boolean'],
            'monitoring_instructions' => ['nullable', 'string', 'max:10000'],
            'monitoring_model' => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('agent-images', 'public');
        }

        unset($validated['image']);

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
        $modelIds = $this->openAiModels->listChatModels();
        $models = $this->mapAndSortModelsWithPricing($modelIds);

        return view('teacher.agents.edit', compact('agent', 'groups', 'models'));
    }

    public function update(Request $request, AgentConfig $agent): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:300'],
            'instructions' => ['required', 'string', 'max:10000'],
            'allowed_groups' => ['nullable', 'array'],
            'allowed_groups.*' => ['integer'],
            'allowed_models' => ['nullable', 'array'],
            'allowed_models.*' => ['string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_enabled' => ['required', 'boolean'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_until' => ['nullable', 'date_format:H:i'],
            'monitoring_is_enabled' => ['required', 'boolean'],
            'monitoring_instructions' => ['nullable', 'string', 'max:10000'],
            'monitoring_model' => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->hasFile('image')) {
            if ($agent->image_path) {
                Storage::disk('public')->delete($agent->image_path);
            }

            $validated['image_path'] = $request->file('image')->store('agent-images', 'public');
        }

        unset($validated['image']);

        $agent->update($validated);

        return redirect()->route('teacher.agents.index')
            ->with('success', 'Agent updated successfully.');
    }

    public function destroy(AgentConfig $agent): RedirectResponse
    {
        if ($agent->image_path) {
            Storage::disk('public')->delete($agent->image_path);
        }

        $agent->delete();

        return redirect()->route('teacher.agents.index')
            ->with('success', 'Agent deleted.');
    }

    /**
     * Build enriched model list with pricing and sort cheapest first.
     * Uses a single overall-per-1M estimate (no input/output split) for ranking and display.
     *
     * @param  array<int,string>  $modelIds
     * @return array<int,array{id:string,display:string,input_per_million:float|null,output_per_million:float|null,cached_input_per_million:float|null,overall_per_million:float|null,sort_key:float}>
     */
    private function mapAndSortModelsWithPricing(array $modelIds): array
    {
        $items = collect($modelIds)->map(function (string $id) {
            $rates = $this->pricing->getRates($id) ?? [];
            $input = $rates['input_per_million'] ?? null;
            $output = $rates['output_per_million'] ?? null;
            $cached = $rates['cached_input_per_million'] ?? null;

            if ($input !== null) {
                $input = (float) $input;
            }
            if ($output !== null) {
                $output = (float) $output;
            }
            if ($cached !== null) {
                $cached = (float) $cached;
            }

            // Compute a single overall per-1M estimate for simpler comparison.
            $overall = null;
            if ($input !== null && $output !== null) {
                $overall = ($input + $output) / 2.0;
            } elseif ($input !== null) {
                $overall = $input;
            } elseif ($output !== null) {
                $overall = $output;
            }

            $sortKey = $overall !== null ? (float) $overall : 999999.0;

            if ($overall !== null) {
                $display = sprintf('  (~$%.3f per 1M)', $overall);
            } else {
                $display = '  (pricing unknown)';
            }

            return [
                'id' => $id,
                'display' => $display,
                'input_per_million' => $input,
                'output_per_million' => $output,
                'cached_input_per_million' => $cached,
                'overall_per_million' => $overall,
                'sort_key' => $sortKey,
            ];
        })
            ->sortBy([['sort_key', 'asc'], ['id', 'asc']])
            ->values()
            ->all();

        return $items;
    }
}
