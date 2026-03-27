@extends('layouts.app')

@section('title', __('New Agent') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
<div class="mx-auto max-w-2xl w-full px-4 py-8">

    <div class="mb-6">
        <a href="{{ route('teacher.agents.index') }}"
           class="text-xs text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
            ← {{ __('Back to Agents') }}
        </a>
        <h1 class="mt-3 text-xl font-semibold text-black dark:text-white">{{ __('New Agent') }}</h1>
    </div>

    <form method="POST"
          action="{{ route('teacher.agents.store') }}">
        @csrf

        <div
             class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 divide-y divide-black/5 dark:divide-white/5">

            <div class="px-5 py-4">
                <label for="name"
                       class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('Name') }}
                </label>
                <input id="name"
                       name="name"
                       type="text"
                       value="{{ old('name') }}"
                       class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15"
                       placeholder="{{ __('e.g. History Tutor') }}"
                       required
                       maxlength="100">
                @error('name')
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
                          maxlength="10000">{{ old('instructions') }}</textarea>
                @error('instructions')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="px-5 py-4">
                <p class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Accessible to Groups') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ __('Select which groups can access this
                    agent. Teachers always have access.') }}</p>
                @if (!empty($groups))
                @php $selectedGroups = array_map('intval', old('allowed_groups', [])); @endphp
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

        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit"
                    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-80 transition-opacity">
                {{ __('Create Agent') }}
            </button>
        </div>
    </form>

</div>
@endsection