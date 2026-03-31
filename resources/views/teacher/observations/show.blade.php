@extends('layouts.app')

@section('title', __('app.teacher.observations.observation') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
    <div class="mx-auto max-w-3xl w-full px-4 py-8">

        <a href="{{ route('teacher.observations.index') }}"
            class="text-xs text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
            ← {{ __('app.teacher.observations.back') }}
        </a>

        <div class="mt-3 flex items-center gap-2 flex-wrap">
            <h1 class="text-xl font-semibold text-black dark:text-white">{{ __('app.teacher.observations.observation') }}
            </h1>
            @if ($observation->agent_name)
                <span
                    class="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-black/5 dark:bg-white/10 text-gray-700 dark:text-gray-300">
                    {{ $observation->agent_name }}
                </span>
            @endif
            @if ($observation->category)
                <span
                    class="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                    {{ $observation->category }}
                </span>
            @endif
        </div>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $observation->student_name ?? __('app.common.unknown_student') }} ·
            {{ \Carbon\Carbon::parse($observation->created_at)->toDayDateTimeString() }}
        </p>

        <div
            class="mt-6 rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 p-5 text-black dark:text-white">
            <div class="prose dark:prose-invert max-w-none whitespace-pre-wrap">{{ $observation->content }}</div>
        </div>

        @if ($observation->conversation_id)
            <div class="mt-4">
                <a href="{{ route('teacher.chats.show', $observation->conversation_id) }}"
                    class="text-sm text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
                    {{ __('app.teacher.observations.view_related_conversation') }}
                </a>
            </div>
        @endif

    </div>
@endsection
