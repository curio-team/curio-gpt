@extends('layouts.app')

@section('title', __('Agent Observations') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
<div class="mx-auto max-w-3xl w-full px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-black dark:text-white">{{ __('Agent Observations') }}</h1>
        <div class="flex items-center gap-4">
            <a href="{{ route('teacher.chats.index') }}"
               class="text-sm text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
                {{ __('View Chats') }}
            </a>
        </div>
    </div>

    @if ($observations->isEmpty())
    <div
         class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 px-6 py-12 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No observations yet.') }}</p>
    </div>
    @else
    <div
         class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 divide-y divide-black/5 dark:divide-white/5">
        @foreach ($observations as $o)
        <a href="{{ route('teacher.observations.show', $o->id) }}"
           class="block px-5 py-4 hover:bg-black/[.02] dark:hover:bg-white/[.02] transition-colors">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-black dark:text-white truncate">
                        {{ $o->student_name ?? __('Unknown student') }}
                    </p>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                        {{ $o->content }}
                    </p>
                </div>
                <div class="shrink-0 text-right">
                    @if ($o->agent_name)
                    <span
                          class="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-black/5 dark:bg-white/10 text-gray-700 dark:text-gray-300">
                        {{ $o->agent_name }}
                    </span>
                    @endif
                    @if ($o->category)
                    <span
                          class="ml-2 inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                        {{ $o->category }}
                    </span>
                    @endif
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-600">
                        {{ \Carbon\Carbon::parse($o->created_at)->diffForHumans() }}
                    </p>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $observations->links() }}
    </div>
    @endif

</div>
@endsection