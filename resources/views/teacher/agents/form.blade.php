@php
    $isEditing = isset($agent);
    $defaultTab = 'general';
    if (
        $errors->has('allowed_groups') ||
        $errors->has('available_from') ||
        $errors->has('available_until') ||
        $errors->has('is_enabled')
    ) {
        $defaultTab = 'access';
    }
    if ($errors->has('allowed_models')) {
        $defaultTab = 'advanced';
    }
    if ($errors->has('attachment')) {
        $defaultTab = 'attachments';
    }
@endphp

<div x-data="{ tab: '{{ $defaultTab }}' }">
    <div class="px-5 pt-4">
        <div class="inline-flex items-center gap-1 rounded-lg border border-black/10 dark:border-white/10 p-1">
            <button type="button" @click="tab = 'general'"
                :class="tab === 'general' ? 'bg-black/10 dark:bg-white/10 text-black dark:text-white' :
                    'text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5'"
                class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors">
                {{ __('app.teacher.agents.form.tabs.general') }}
            </button>
            <button type="button" @click="tab = 'access'"
                :class="tab === 'access' ? 'bg-black/10 dark:bg-white/10 text-black dark:text-white' :
                    'text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5'"
                class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors">
                {{ __('app.teacher.agents.form.tabs.access') }}
            </button>
            <button type="button" @click="tab = 'advanced'"
                :class="tab === 'advanced' ? 'bg-black/10 dark:bg-white/10 text-black dark:text-white' :
                    'text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5'"
                class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors">
                {{ __('app.teacher.agents.form.tabs.advanced') }}
            </button>
            <button type="button" @click="tab = 'monitoring'"
                :class="tab === 'monitoring' ? 'bg-black/10 dark:bg-white/10 text-black dark:text-white' :
                    'text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5'"
                class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors">
                {{ __('app.teacher.agents.form.tabs.monitoring') }}
            </button>
            @if ($isEditing)
                <button type="button" @click="tab = 'attachments'"
                    :class="tab === 'attachments' ? 'bg-black/10 dark:bg-white/10 text-black dark:text-white' :
                        'text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5'"
                    class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors">
                    {{ __('app.teacher.agents.form.tabs.attachments') }}
                </button>
            @endif
        </div>
    </div>

    <div x-show="tab === 'general'" x-cloak>
        <div class="px-5 py-4">
            <label for="name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('app.teacher.agents.form.name') }}
            </label>
            <input id="name" name="name" type="text" value="{{ old('name', $agent->name ?? '') }}"
                class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15"
                placeholder="{{ __('app.teacher.agents.form.name_placeholder') }}" required maxlength="100">
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="px-5 py-4">
            <label for="description" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('app.teacher.agents.form.short_description') }}
            </label>
            <input id="description" name="description" type="text"
                value="{{ old('description', $agent->description ?? '') }}"
                class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15"
                placeholder="{{ __('app.teacher.agents.form.short_description_placeholder') }}" maxlength="300">
            @error('description')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="px-5 py-4">
            <label for="image" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('app.teacher.agents.form.image') }}
            </label>
            @if ($isEditing && $agent->image_path)
                <div class="mb-2">
                    <img src="{{ $agent->image_url }}" alt="{{ $agent->name }}"
                        class="h-20 w-20 rounded-lg object-cover border border-black/10 dark:border-white/10">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('app.teacher.agents.form.upload_new_image') }}</p>
                </div>
            @endif
            <input id="image" name="image" type="file" accept="image/*"
                class="w-full text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-black/5 dark:file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-black dark:file:text-white hover:file:opacity-80 transition-opacity">
            @error('image')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="px-5 py-4">
            <label for="instructions" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('app.teacher.agents.form.instructions') }}
            </label>
            <textarea id="instructions" name="instructions" rows="8"
                class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15 resize-y"
                placeholder="{{ __('app.teacher.agents.form.instructions_placeholder') }}" required maxlength="50000">{{ old('instructions', $agent->instructions ?? '') }}</textarea>
            @error('instructions')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="px-5 pt-4" x-show="false"></div>

    <div x-show="tab === 'access'" x-cloak>
        <div class="px-5 py-4" x-data="{
            enabled: {{ old('is_enabled', $isEditing ? ($agent->is_enabled ? '1' : '0') : '1') !== '0' ? 'true' : 'false' }},
            hasTimeWindow: {{ old('available_from', $isEditing ? $agent->available_from ?? '' : '') !== '' || old('available_until', $isEditing ? $agent->available_until ?? '' : '') !== '' ? 'true' : 'false' }},
            availableFrom: '{{ old('available_from', $isEditing && $agent->available_from ? substr($agent->available_from, 0, 5) : '') }}',
            availableUntil: '{{ old('available_until', $isEditing && $agent->available_until ? substr($agent->available_until, 0, 5) : '') }}'
        }" x-init="$watch('hasTimeWindow', val => {
            if (!val) {
                availableFrom = '';
                availableUntil = '';
            }
        })">
            <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-3">
                {{ __('app.teacher.agents.form.availability') }}
            </p>

            <input type="hidden" name="is_enabled" :value="enabled ? '1' : '0'">

            <x-toggle model="enabled" :on-label="__('app.teacher.agents.form.enabled')" :off-label="__('app.teacher.agents.form.disabled')" class="mb-4" />

            <div x-show="enabled">
                <x-toggle model="hasTimeWindow" :on-label="__('app.teacher.agents.form.restricted_time_window')" :off-label="__('app.teacher.agents.form.not_restricted_time_window')" class="mb-4" />

                <div x-show="hasTimeWindow" class="flex items-end gap-3">
                    <div>
                        <label
                            class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('app.teacher.agents.form.from') }}</label>
                        <input type="time" name="available_from" x-model="availableFrom"
                            class="rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
                        @error('available_from')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <span class="pb-2 text-gray-400">–</span>
                    <div>
                        <label
                            class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('app.teacher.agents.form.until') }}</label>
                        <input type="time" name="available_until" x-model="availableUntil"
                            class="rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
                        @error('available_until')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="pb-2 text-xs text-gray-400 dark:text-gray-500">
                        {{ __('app.teacher.agents.form.current_server_time') }} {{ now()->format('H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="px-5 py-4" x-data="{ historyDisabled: {{ old('history_is_disabled', $isEditing ? ($agent->history_is_disabled ? '1' : '0') : '0') !== '0' ? 'true' : 'false' }} }">
            <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-3">
                {{ __('app.teacher.agents.form.conversation_history') }}
            </p>

            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                {{ __('app.teacher.agents.form.conversation_history_help') }}</p>

            <input type="hidden" name="history_is_disabled" :value="historyDisabled ? '1' : '0'">

            <x-toggle model="historyDisabled" :on-label="__('app.teacher.agents.form.history_disabled')" :off-label="__('app.teacher.agents.form.history_enabled')" class="mb-1" />
        </div>

        <div class="px-5 py-4">
            <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-3">
                {{ __('app.teacher.agents.form.turn_limit') }}
            </p>

            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                {{ __('app.teacher.agents.form.turn_limit_help') }}
            </p>

            <input id="turn_limit" name="turn_limit" type="number" min="1" max="1000"
                value="{{ old('turn_limit', $agent->turn_limit ?? 25) }}"
                class="w-32 rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
            @error('turn_limit')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="px-5 py-4">
            <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('app.teacher.agents.form.groups_heading') }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ __('app.teacher.agents.form.groups_help') }}
            </p>
            @if (!empty($groups))
                @php $selectedGroups = array_map('intval', old('allowed_groups', $isEditing ? $agent->allowed_groups ?? [] : [])); @endphp
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
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="allowed_groups[]" :value="id">
                    </template>
                    <div class="flex items-center gap-2 mb-2">
                        <input x-model="search" type="text"
                            placeholder="{{ __('app.teacher.agents.form.search_groups') }}" @keydown.enter.prevent
                            class="flex-1 rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-1.5 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
                        <button type="button" @click="checkAll()"
                            class="shrink-0 rounded-lg border border-black/10 dark:border-white/10 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            {{ __('app.teacher.agents.form.select_all') }}
                        </button>
                        <button type="button" @click="checkNone()"
                            class="shrink-0 rounded-lg border border-black/10 dark:border-white/10 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            {{ __('app.teacher.agents.form.select_none') }}
                        </button>
                    </div>
                    <div class="h-52 overflow-y-auto rounded-lg border border-black/10 dark:border-white/10 p-1">
                        <template x-for="group in filtered" :key="group.id">
                            <label
                                class="flex items-center gap-2 px-2 py-1.5 rounded-md cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 text-sm text-black dark:text-white">
                                <input type="checkbox" :checked="isSelected(group.id)" @change="toggle(group.id)"
                                    class="rounded border-black/20 dark:border-white/20 text-black focus:ring-black/10 dark:focus:ring-white/15">
                                <span x-text="group.name" class="truncate"></span>
                            </label>
                        </template>
                        <p x-show="filtered.length === 0"
                            class="py-4 text-center text-xs text-gray-400 dark:text-gray-500 italic">
                            {{ __('app.teacher.agents.form.no_groups_match') }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-400 dark:text-gray-500 italic">
                    {{ __('app.teacher.agents.form.no_groups_available') }}</p>
            @endif
            @error('allowed_groups')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div x-show="tab === 'advanced'" x-cloak>
        @if (isset($models) && is_array($models) && count($models) > 0)
            <div class="px-5 py-4">
                <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('app.teacher.agents.form.student_selectable_models') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    {{ __('app.teacher.agents.form.student_selectable_models_help') }}
                </p>
                @php $selectedModels = array_values(old('allowed_models', $isEditing ? $agent->allowed_models ?? [] : [])); @endphp
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
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="allowed_models[]" :value="id">
                    </template>
                    <div class="flex items-center gap-2 mb-2">
                        <input x-model="search" type="text"
                            placeholder="{{ __('app.teacher.agents.form.search_models') }}" @keydown.enter.prevent
                            class="flex-1 rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-1.5 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
                        <button type="button" @click="checkAll()"
                            class="shrink-0 rounded-lg border border-black/10 dark:border-white/10 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            {{ __('app.teacher.agents.form.select_all') }}
                        </button>
                        <button type="button" @click="checkNone()"
                            class="shrink-0 rounded-lg border border-black/10 dark:border-white/10 px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            {{ __('app.teacher.agents.form.select_none') }}
                        </button>
                    </div>
                    <div class="h-52 overflow-y-auto rounded-lg border border-black/10 dark:border-white/10 p-1">
                        <template x-for="m in filtered" :key="m.id">
                            <label
                                class="flex items-center gap-2 px-2 py-1.5 rounded-md cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 text-sm text-black dark:text-white">
                                <input type="checkbox" :checked="isSelected(m.id)" @change="toggle(m.id)"
                                    class="rounded border-black/20 dark:border-white/20 text-black focus:ring-black/10 dark:focus:ring-white/15">
                                <div class="flex w-full justify-between">
                                    <span x-text="m.id"></span>
                                    <span x-text="m.display" class="opacity-50"></span>
                                </div>
                            </label>
                        </template>
                        <p x-show="filtered.length === 0"
                            class="py-4 text-center text-xs text-gray-400 dark:text-gray-500 italic">
                            {{ __('app.teacher.agents.form.no_models_match') }}
                        </p>
                    </div>
                </div>
                @error('allowed_models')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        @endif
    </div>

    <div x-show="tab === 'monitoring'" x-cloak>
        <div class="px-5 py-4" x-data="{ monitoringEnabled: {{ old('monitoring_is_enabled', $isEditing ? ($agent->monitoring_is_enabled ? '1' : '0') : '0') !== '0' ? 'true' : 'false' }} }">

            <p class="mb-4 block text-sm text-gray-700 dark:text-gray-400 mb-2">
                {{ __('app.teacher.agents.form.monitoring_help') }}
            </p>

            <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-3">
                {{ __('app.teacher.agents.form.tabs.monitoring') }}
            </p>

            <input type="hidden" name="monitoring_is_enabled" :value="monitoringEnabled ? '1' : '0'">

            <x-toggle model="monitoringEnabled" :on-label="__('app.teacher.agents.form.monitoring_enabled')" :off-label="__('app.teacher.agents.form.monitoring_disabled')" class="mb-4" />

            <div x-show="monitoringEnabled">
                <div class="mb-4">
                    <label for="monitoring_instructions"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        {{ __('app.teacher.agents.form.monitoring_instructions') }}
                    </label>
                    <textarea id="monitoring_instructions" name="monitoring_instructions" rows="6"
                        class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15 resize-y"
                        placeholder="{{ __('app.teacher.agents.form.monitoring_instructions_placeholder') }}" maxlength="10000">{{ old('monitoring_instructions', $agent->monitoring_instructions ?? '') }}</textarea>
                    @error('monitoring_instructions')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                @if (isset($models) && is_array($models) && count($models) > 0)
                    <div class="mb-2">
                        <label for="monitoring_model"
                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ __('app.teacher.agents.form.monitoring_model') }}
                        </label>
                        @php $currentMonitoringModel = old('monitoring_model', $isEditing ? $agent->monitoring_model ?? '' : ''); @endphp
                        <select id="monitoring_model" name="monitoring_model"
                            class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
                            <option value="">{{ __('app.teacher.agents.form.use_system_default') }}</option>
                            @foreach ($models as $m)
                                <option value="{{ $m['id'] }}" @if ($currentMonitoringModel === $m['id']) selected @endif>
                                    {{ $m['id'] }} {{ $m['display'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('monitoring_model')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($isEditing)
        <div x-show="tab === 'attachments'" x-cloak>
            <div class="px-5 py-4">
                <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-3">
                    {{ __('app.teacher.agents.form.teacher_attachments') }}
                </p>

                <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('app.teacher.agents.form.attachments_help') }}
                </p>

                <div class="mb-4 flex items-center gap-3">
                    <input id="attachment" name="attachment" type="file" form="agent-attachments-upload"
                        class="flex-1 text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-black/5 dark:file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-black dark:file:text-white hover:file:opacity-80 transition-opacity"
                        required>
                    <button type="submit" form="agent-attachments-upload"
                        class="shrink-0 inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-80 transition-opacity">
                        {{ __('app.common.upload') }}
                    </button>
                </div>
                @error('attachment')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

                @php $attachments = $agent->attachments ?? []; @endphp
                <div
                    class="rounded-lg border border-black/10 dark:border-white/10 divide-y divide-black/5 dark:divide-white/5">
                    @forelse ($attachments as $att)
                        <div class="flex items-center justify-between px-3 py-2 text-sm">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-black dark:text-white">
                                    {{ $att['name'] ?? basename($att['storage_path'] ?? '') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $att['mime'] ?? 'application/octet-stream' }}
                                    <span class="mx-1">·</span>
                                    @php $size = (int) ($att['size'] ?? 0); @endphp
                                    {{ $size > 0 ? number_format($size / 1024, 1) . ' KB' : '' }}
                                    @if (!empty($att['uploaded_at']))
                                        <span class="mx-1">·</span>
                                        {{ \Illuminate\Support\Carbon::parse($att['uploaded_at'])->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                            <div class="shrink-0 flex items-center gap-2">
                                <a href="{{ route('teacher.agents.attachments.download', [$agent, $att['id'] ?? '']) }}"
                                    class="rounded-lg border border-black/10 dark:border-white/10 px-2 py-1 text-xs text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">{{ __('app.common.download') }}</a>
                                <button type="submit" form="agent-attachment-delete-{{ $att['id'] ?? '' }}"
                                    onclick="return confirm('{{ __('app.teacher.agents.form.delete_attachment_confirm') }}')"
                                    class="rounded-lg border border-black/10 dark:border-white/10 px-2 py-1 text-xs text-red-600 dark:text-red-400 hover:bg-red-50/50 dark:hover:bg-red-900/20 transition-colors">{{ __('app.common.delete') }}</button>
                            </div>
                        </div>
                    @empty
                        <p class="px-3 py-6 text-center text-xs text-gray-400 dark:text-gray-500 italic">
                            {{ __('app.teacher.agents.form.no_attachments_yet') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
