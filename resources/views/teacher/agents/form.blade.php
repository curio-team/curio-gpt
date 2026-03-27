@php
$isEditing = isset($agent);
@endphp

<div class="px-5 py-4">
    <label for="name"
           class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
        {{ __('Name') }}
    </label>
    <input id="name"
           name="name"
           type="text"
           value="{{ old('name', $agent->name ?? '') }}"
           class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15"
           placeholder="{{ __('e.g. History Tutor') }}"
           required
           maxlength="100">
    @error('name')
    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="px-5 py-4">
    <label for="description"
           class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
        {{ __('Short Description') }}
    </label>
    <input id="description"
           name="description"
           type="text"
           value="{{ old('description', $agent->description ?? '') }}"
           class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15"
           placeholder="{{ __('e.g. Helps you study history topics') }}"
           maxlength="300">
    @error('description')
    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="px-5 py-4">
    <label for="image"
           class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
        {{ __('Image') }}
    </label>
    @if ($isEditing && $agent->image_path)
    <div class="mb-2">
        <img src="{{ $agent->image_url }}"
             alt="{{ $agent->name }}"
             class="h-20 w-20 rounded-lg object-cover border border-black/10 dark:border-white/10">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Upload a new image to replace the current one.')
            }}</p>
    </div>
    @endif
    <input id="image"
           name="image"
           type="file"
           accept="image/*"
           class="w-full text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-black/5 dark:file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-black dark:file:text-white hover:file:opacity-80 transition-opacity">
    @error('image')
    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

<div class="px-5 py-4">
    <label for="instructions"
           class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
        {{ __('Instructions') }}
    </label>
    <textarea id="instructions"
              name="instructions"
              rows="8"
              class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15 resize-y"
              placeholder="{{ __('Describe how the agent should behave…') }}"
              required
              maxlength="10000">{{ old('instructions', $agent->instructions ?? '') }}</textarea>
    @error('instructions')
    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

@if (isset($models) && is_array($models) && count($models) > 0)
<div class="px-5 py-4">
    <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ __('Student-Selectable Models') }}
    </p>
    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
        {{ __('Choose which OpenAI models students can pick for this agent. Sorted by overall estimated price per 1M
        tokens (cheapest first). Leave empty to always use the system default.') }}
    </p>
    @php $selectedModels = array_values(old('allowed_models', $isEditing ? ($agent->allowed_models ?? []) : []));
    @endphp
    <div x-data="{
        search: '',
        models: @js($models),
        selected: @js($selectedModels),
        get filtered() {
            if (!this.search) return this.models;
            const q = this.search.toLowerCase();
            return this.models.filter(m => (m.id && m.id.toLowerCase().includes(q)) || (m.display && m.display.toLowerCase().includes(q)));
        },
        isSelected(id) { return this.selected.includes(id); },
        toggle(id) {
            const idx = this.selected.indexOf(id);
            if (idx === -1) { this.selected.push(id); } else { this.selected.splice(idx, 1); }
        },
        checkAll() { this.filtered.forEach(m => { if (!this.selected.includes(m.id)) this.selected.push(m.id); }); },
        checkNone() {
            const ids = this.filtered.map(m => m.id);
            this.selected = this.selected.filter(id => !ids.includes(id));
        }
    }">
        <template x-for="id in selected"
                  :key="id">
            <input type="hidden"
                   name="allowed_models[]"
                   :value="id">
        </template>
        <div class="flex items-center gap-2 mb-2">
            <input x-model="search"
                   type="text"
                   placeholder="{{ __('Search models…') }}"
                   @keydown.enter.prevent
                   class="flex-1 rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-1.5 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
            <button type="button"
                    @click="checkAll()"
                    class="shrink-0 rounded-lg border border-black/10 dark:border-white/10 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                {{ __('Select all') }}
            </button>
            <button type="button"
                    @click="checkNone()"
                    class="shrink-0 rounded-lg border border-black/10 dark:border-white/10 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                {{ __('Select none') }}
            </button>
        </div>
        <div class="h-52 overflow-y-auto rounded-lg border border-black/10 dark:border-white/10 p-1">
            <template x-for="m in filtered"
                      :key="m.id">
                <label
                       class="flex items-center gap-2 px-2 py-1.5 rounded-md cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 text-sm text-black dark:text-white">
                    <input type="checkbox"
                           :checked="isSelected(m.id)"
                           @change="toggle(m.id)"
                           class="rounded border-black/20 dark:border-white/20 text-black focus:ring-black/10 dark:focus:ring-white/15">
                    <div class="flex w-full justify-between">
                        <span x-text="m.id"></span>
                        <span x-text="m.display"
                              class="opacity-50"></span>
                    </div>
                </label>
            </template>
            <p x-show="filtered.length === 0"
               class="py-4 text-center text-xs text-gray-400 dark:text-gray-500 italic">
                {{ __('No models match your search.') }}
            </p>
        </div>
    </div>
    @error('allowed_models')
    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
@endif

<div class="px-5 py-4"
     x-data="{
        enabled: {{ old('is_enabled', $isEditing ? ($agent->is_enabled ? '1' : '0') : '1') !== '0' ? 'true' : 'false' }},
        hasTimeWindow: {{ (old('available_from', $isEditing ? ($agent->available_from ?? '') : '') !== '' || old('available_until', $isEditing ? ($agent->available_until ?? '') : '') !== '') ? 'true' : 'false' }},
        availableFrom: '{{ old('available_from', $isEditing && $agent->available_from ? substr($agent->available_from, 0, 5) : '') }}',
        availableUntil: '{{ old('available_until', $isEditing && $agent->available_until ? substr($agent->available_until, 0, 5) : '') }}'
    }"
     x-init="$watch('hasTimeWindow', val => { if (!val) { availableFrom = ''; availableUntil = ''; } })">
    <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-3">
        {{ __('Availability') }}
    </p>

    <input type="hidden"
           name="is_enabled"
           :value="enabled ? '1' : '0'">

    <x-toggle model="enabled"
              :on-label="__('Enabled')"
              :off-label="__('Disabled')"
              class="mb-4" />

    <div x-show="enabled">
        <x-toggle model="hasTimeWindow"
                  :on-label="__('Restricted to a time window')"
                  :off-label="__('Not restricted to a time window')"
                  class="mb-4" />

        <div x-show="hasTimeWindow"
             class="flex items-end gap-3">
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('From') }}</label>
                <input type="time"
                       name="available_from"
                       x-model="availableFrom"
                       class="rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
                @error('available_from')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <span class="pb-2 text-gray-400">–</span>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Until') }}</label>
                <input type="time"
                       name="available_until"
                       x-model="availableUntil"
                       class="rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
                @error('available_until')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="pb-2 text-xs text-gray-400 dark:text-gray-500">
                {{ __('Current server time:') }} {{ now()->format('H:i') }}
            </div>
        </div>
    </div>
</div>

<div class="px-5 py-4">
    <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ __('Accessible to Groups') }}
    </p>
    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ __('Select which groups can access this agent. Teachers
        always have access.') }}</p>
    @if (!empty($groups))
    @php $selectedGroups = array_map('intval', old('allowed_groups', $isEditing ? ($agent->allowed_groups ?? []) : []));
    @endphp
    <div x-data="{
        search: '',
        groups: @js($groups),
        selected: @js($selectedGroups),
        get filtered() {
            if (!this.search) return this.groups;
            const q = this.search.toLowerCase();
            return this.groups.filter(g => g.name.toLowerCase().includes(q));
        },
        isSelected(id) { return this.selected.includes(id); },
        toggle(id) {
            const idx = this.selected.indexOf(id);
            if (idx === -1) { this.selected.push(id); } else { this.selected.splice(idx, 1); }
        },
        checkAll() {
            this.filtered.forEach(g => { if (!this.selected.includes(g.id)) this.selected.push(g.id); });
        },
        checkNone() {
            const ids = this.filtered.map(g => g.id);
            this.selected = this.selected.filter(id => !ids.includes(id));
        }
    }">
        <template x-for="id in selected"
                  :key="id">
            <input type="hidden"
                   name="allowed_groups[]"
                   :value="id">
        </template>
        <div class="flex items-center gap-2 mb-2">
            <input x-model="search"
                   type="text"
                   placeholder="{{ __('Search groups…') }}"
                   @keydown.enter.prevent
                   class="flex-1 rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-1.5 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
            <button type="button"
                    @click="checkAll()"
                    class="shrink-0 rounded-lg border border-black/10 dark:border-white/10 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                {{ __('Select all') }}
            </button>
            <button type="button"
                    @click="checkNone()"
                    class="shrink-0 rounded-lg border border-black/10 dark:border-white/10 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                {{ __('Select none') }}
            </button>
        </div>
        <div class="h-52 overflow-y-auto rounded-lg border border-black/10 dark:border-white/10 p-1">
            <template x-for="group in filtered"
                      :key="group.id">
                <label
                       class="flex items-center gap-2 px-2 py-1.5 rounded-md cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 text-sm text-black dark:text-white">
                    <input type="checkbox"
                           :checked="isSelected(group.id)"
                           @change="toggle(group.id)"
                           class="rounded border-black/20 dark:border-white/20 text-black focus:ring-black/10 dark:focus:ring-white/15">
                    <span x-text="group.name"
                          class="truncate"></span>
                </label>
            </template>
            <p x-show="filtered.length === 0"
               class="py-4 text-center text-xs text-gray-400 dark:text-gray-500 italic">
                {{ __('No groups match your search.') }}
            </p>
        </div>
    </div>
    @else
    <p class="text-xs text-gray-400 dark:text-gray-500 italic">{{ __('No groups available.') }}</p>
    @endif
    @error('allowed_groups')
    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>