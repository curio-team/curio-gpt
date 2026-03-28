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

        <form method="GET" action="{{ route('teacher.chats.index') }}" class="mb-6">
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search chats…"
                        class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-black/20 dark:focus:ring-white/20" />
                </div>
                @if (request()->filled('q'))
                    <a href="{{ route('teacher.chats.index') }}"
                        class="text-sm text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
                        Clear
                    </a>
                @endif
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-black text-white dark:bg-white dark:text-black px-3 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                    Search
                </button>
            </div>
        </form>

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
                        class="flex items-center justify-between gap-4 px-5 py-4 hover:bg-black/20 dark:hover:bg-white/20 transition-colors">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-black dark:text-white truncate">
                                {{ $conversation->student_name ?? __('app.common.unknown_student') }}
                            </p>
                            @php
                                $search = request('q');
                                $highlightHtml = null;

                                if (!empty($search)) {
                                    $candidate = $conversation->match_message ?? null;

                                    if (is_string($candidate) && $candidate !== '') {
                                        $lines = preg_split("/(\r\n|\n|\r)/", $candidate) ?: [];
                                        $line = collect($lines)->first(function ($ln) use ($search) {
                                            return stripos($ln, $search) !== false;
                                        });
                                        $line = $line ?? ($lines[0] ?? '');

                                        $escaped = e($line);
                                        $pattern = '/' . preg_quote($search, '/') . '/i';
                                            $highlightHtml = preg_replace(
                                                $pattern,
                                                '<mark class="bg-yellow-200 dark:bg-yellow-600/40 px-0.5 rounded">$0</mark>',
                                                $escaped
                                            );
                                    } else {
                                        $titleEscaped = e($conversation->title ?? '');
                                        $pattern = '/' . preg_quote($search, '/') . '/i';
                                            $highlightHtml = preg_replace(
                                                $pattern,
                                                '<mark class="bg-yellow-200 dark:bg-yellow-600/40 px-0.5 rounded">$0</mark>',
                                                $titleEscaped
                                            );
                                    }
                                }
                            @endphp

                            @if (!empty($search))
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {!! $highlightHtml !!}
                                </p>
                            @else
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $conversation->title }}
                                </p>
                            @endif
                        </div>
                        <div class="shrink-0 text-right">
                            @if ($conversation->agent_name)
                                <span
                                    class="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-black/5 dark:bg-white/10 text-gray-700 dark:text-gray-300">
                                    {{ $conversation->agent_name }}
                                </span>
                            @else
                                <span
                                    class="text-xs text-gray-400 dark:text-gray-600">{{ __('app.common.no_agent') }}</span>
                            @endif
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-600">
                                {{ \Carbon\Carbon::parse($conversation->updated_at)->diffForHumans() }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $conversations->withQueryString()->links() }}
            </div>
        @endif

    </div>
@endsection
