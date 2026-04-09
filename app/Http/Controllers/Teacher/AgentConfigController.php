<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AgentConfig;
use App\Services\ModelPricingService;
use App\Services\OpenAiModelService;
use App\Services\SdApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Ai\Files\Document as AiDocument;

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
            'instructions' => ['required', 'string', 'max:50000'],
            'allowed_groups' => ['nullable', 'array'],
            'allowed_groups.*' => ['integer'],
            'allowed_models' => ['nullable', 'array'],
            'allowed_models.*' => ['string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_enabled' => ['required', 'boolean'],
            'history_is_disabled' => ['required', 'boolean'],
            'turn_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_until' => ['nullable', 'date_format:H:i'],
            'monitoring_is_enabled' => ['required', 'boolean'],
            'monitoring_instructions' => ['nullable', 'string', 'max:50000'],
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
            ->with('success', __('app.teacher.agents.flash.created'));
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
            'instructions' => ['required', 'string', 'max:50000'],
            'allowed_groups' => ['nullable', 'array'],
            'allowed_groups.*' => ['integer'],
            'allowed_models' => ['nullable', 'array'],
            'allowed_models.*' => ['string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_enabled' => ['required', 'boolean'],
            'history_is_disabled' => ['required', 'boolean'],
            'turn_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'available_from' => ['nullable', 'date_format:H:i'],
            'available_until' => ['nullable', 'date_format:H:i'],
            'monitoring_is_enabled' => ['required', 'boolean'],
            'monitoring_instructions' => ['nullable', 'string', 'max:50000'],
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
            ->with('success', __('app.teacher.agents.flash.updated'));
    }

    public function destroy(AgentConfig $agent): RedirectResponse
    {
        if ($agent->image_path) {
            Storage::disk('public')->delete($agent->image_path);
        }

        $agent->delete();

        return redirect()->route('teacher.agents.index')
            ->with('success', __('app.teacher.agents.flash.deleted'));
    }

    /**
     * Revoke student access to all conversation history for this agent.
     * Sets revoked_at on all related conversations; data remains for teachers.
     */
    public function revokeHistory(AgentConfig $agent): RedirectResponse
    {
        DB::table('agent_conversations')
            ->where('agent_config_id', $agent->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        return redirect()
            ->route('teacher.agents.index')
            ->with('success', __('app.teacher.agents.flash.history_revoked'));
    }

    /**
     * Upload an attachment for the given agent: store privately and upload to provider.
     */
    public function storeAttachment(Request $request, AgentConfig $agent): RedirectResponse
    {
        $validated = $request->validate([
            'attachment' => ['required', 'file', 'max:20480'], // 20MB limit
        ]);

        try {
            $file = $validated['attachment'];

            // Store privately on local disk
            $path = $file->store('agent-attachments/' . $agent->id, 'local');

            // Upload to provider via Laravel AI SDK
            $stored = AiDocument::fromStorage($path, disk: 'local')->put();

            $attachments = $agent->attachments ?? [];
            $attachments[] = [
                'id' => (string) Str::uuid7(),
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'storage_path' => $path,
                'provider' => 'openai',
                'provider_file_id' => $stored->id ?? null,
                'uploaded_at' => now()->toISOString(),
            ];

            $agent->update(['attachments' => $attachments]);

            return back()->with('success', __('app.teacher.agents.flash.attachment_uploaded'));
        } catch (\Throwable $e) {
            // Best-effort cleanup of local file if we created one but provider failed
            if (isset($path)) {
                Storage::disk('local')->delete($path);
            }

            return back()->withErrors(['attachment' => __('app.teacher.agents.flash.upload_failed', ['msg' => $e->getMessage()])]);
        }
    }

    /**
     * Download a previously uploaded attachment from private storage.
     */
    public function downloadAttachment(AgentConfig $agent, string $attachmentId)
    {
        $attachment = collect($agent->attachments ?? [])->firstWhere('id', $attachmentId);
        abort_if(! $attachment, 404);

        $path = $attachment['storage_path'] ?? null;
        abort_if(! $path || ! Storage::disk('local')->exists($path), 404);

        $filename = $attachment['name'] ?? basename($path);
        $mime = $attachment['mime'] ?? null;

        return Storage::disk('local')->download($path, $filename, array_filter([
            'Content-Type' => $mime,
        ]));
    }

    /**
     * Delete an attachment from provider and private storage.
     */
    public function destroyAttachment(AgentConfig $agent, string $attachmentId): RedirectResponse
    {
        $attachments = collect($agent->attachments ?? []);
        $attachment = $attachments->firstWhere('id', $attachmentId);

        if (! $attachment) {
            return back()->withErrors(['attachment' => __('app.teacher.agents.flash.attachment_not_found')]);
        }

        // Delete from provider (best-effort)
        try {
            if (! empty($attachment['provider_file_id'])) {
                AiDocument::fromId($attachment['provider_file_id'])->delete();
            }
        } catch (\Throwable $e) {
            // ignore provider deletion failures
        }

        // Delete local file
        if (! empty($attachment['storage_path'])) {
            Storage::disk('local')->delete($attachment['storage_path']);
        }

        $remaining = $attachments->reject(fn($a) => ($a['id'] ?? null) === $attachmentId)->values()->all();
        $agent->update(['attachments' => $remaining]);

        return back()->with('success', __('app.teacher.agents.flash.attachment_deleted'));
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
                $display = '  ' . __('app.teacher.agents.form.per_million_cost', ['price' => number_format($overall, 3)]);
            } else {
                $display = '  (' . __('app.teacher.agents.form.pricing_unknown') . ')';
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
