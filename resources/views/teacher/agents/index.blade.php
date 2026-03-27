@extends('layouts.app')

@section('title', __('app.teacher.agents.manage_title') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
<div class="mx-auto max-w-3xl w-full px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-black dark:text-white">{{ __('app.teacher.agents.agents') }}</h1>
        <a href="{{ route('teacher.agents.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-80 transition-opacity">
            {{ __('app.teacher.agents.new_agent') }}
        </a>
    </div>

    @if (session('success'))
    <div
         class="mb-4 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/30 px-4 py-2.5 text-sm text-green-800 dark:text-green-300">
        {{ session('success') }}
    </div>
    @endif

    @if ($agents->isEmpty())
    <div
         class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 px-6 py-12 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.teacher.agents.no_agents_yet') }}</p>
    </div>
    @else
    <div
         class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 divide-y divide-black/5 dark:divide-white/5">
        @foreach ($agents as $agent)
        <div class="flex items-start gap-3 px-5 py-4">

            {{-- Image / avatar --}}
            <div class="shrink-0 mt-0.5">
                @if ($agent->image_path)
                <img src="{{ $agent->image_url }}"
                     alt="{{ $agent->name }}"
                     class="h-10 w-10 rounded-lg object-cover border border-black/10 dark:border-white/10">
                @else
                <div
                     class="flex h-10 w-10 items-center justify-center rounded-lg border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5">
                    <span class="text-sm font-semibold text-gray-400 dark:text-gray-500">{{
                        strtoupper(mb_substr($agent->name, 0, 1)) }}</span>
                </div>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-1.5">
                    <p class="text-sm font-medium text-black dark:text-white">{{ $agent->name }}</p>

                    {{-- Availability badge --}}
                    @if (! $agent->is_enabled)
                    <span
                          class="inline-flex items-center rounded-full border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/40 px-2 py-0.5 text-xs font-medium text-red-600 dark:text-red-400">
                        {{ __('app.common.disabled') }}
                    </span>
                    @elseif ($agent->available_from || $agent->available_until)
                    <span
                          class="inline-flex items-center gap-1 rounded-full border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 text-xs font-medium text-amber-700 dark:text-amber-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 dark:bg-amber-400"></span>
                        {{ substr($agent->available_from ?? '??:??', 0, 5) }} – {{ substr($agent->available_until ??
                        '??:??', 0, 5) }}
                    </span>
                    @else
                    <span
                          class="inline-flex items-center gap-1 rounded-full border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/40 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500 dark:bg-green-400"></span>
                        {{ __('app.teacher.agents.always_available') }}
                    </span>
                    @endif
                </div>

                @if ($agent->description)
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ $agent->description }}</p>
                @endif

                @if (! empty($agent->allowed_groups))
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    {{ collect($agent->allowed_groups)->map(fn ($id) => data_get($groups->get($id), 'name',
                    '#'.$id))->implode(', ') }}
                </p>
                @endif
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('teacher.agents.edit', $agent) }}"
                   class="text-xs text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
                    {{ __('app.common.edit') }}
                </a>

                <form method="POST"
                      action="{{ route('teacher.agents.revokeHistory', $agent) }}"
                      onsubmit="return confirm('{{ __('app.teacher.agents.revoke_history_confirm') }}')">
                    @csrf
                    <button type="submit"
                            class="cursor-pointer text-xs text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 transition-colors">
                        {{ __('app.teacher.agents.revoke_history') }}
                    </button>
                </form>
                <form method="POST"
                      action="{{ route('teacher.agents.destroy', $agent) }}"
                      onsubmit="return confirm('{{ __('app.teacher.agents.delete_confirm') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="cursor-pointer text-xs text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors">
                        {{ __('app.common.delete') }}
                    </button>
                </form>
            </div>

        </div>
        @endforeach
    </div>
    @endif

    <div class="mt-8">
        <a href="{{ route('teacher.chats.index') }}"
           class="text-sm text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
            {{ __('app.teacher.agents.view_all_student_chats') }}
        </a>
    </div>

</div>
@endsection