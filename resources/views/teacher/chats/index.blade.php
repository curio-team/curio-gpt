@extends('layouts.app')

@section('title', __('app.teacher.chats.title') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
<div class="mx-auto max-w-3xl w-full px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-black dark:text-white">{{ __('app.teacher.chats.title') }}</h1>
        <div class="flex items-center gap-4">
            <a href="{{ route('teacher.usage.index') }}"
               class="text-sm text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
                {{ __('app.teacher.chats.view_usage') }}
            </a>
        </div>
    </div>

    @if ($conversations->isEmpty())
    <div
         class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 px-6 py-12 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.teacher.chats.no_chats_yet') }}</p>
    </div>
    @else
    <div
         class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 divide-y divide-black/5 dark:divide-white/5">
        @foreach ($conversations as $conversation)
        <a href="{{ route('teacher.chats.show', $conversation->id) }}"
           class="flex items-center justify-between gap-4 px-5 py-4 hover:bg-black/[.02] dark:hover:bg-white/[.02] transition-colors">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-black dark:text-white truncate">
                    {{ $conversation->student_name ?? __('app.common.unknown_student') }}
                </p>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ $conversation->title }}
                </p>
            </div>
            <div class="shrink-0 text-right">
                @if ($conversation->agent_name)
                <span
                      class="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-black/5 dark:bg-white/10 text-gray-700 dark:text-gray-300">
                    {{ $conversation->agent_name }}
                </span>
                @else
                <span class="text-xs text-gray-400 dark:text-gray-600">{{ __('app.common.no_agent') }}</span>
                @endif
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-600">
                    {{ \Carbon\Carbon::parse($conversation->updated_at)->diffForHumans() }}
                </p>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $conversations->links() }}
    </div>
    @endif

</div>
@endsection