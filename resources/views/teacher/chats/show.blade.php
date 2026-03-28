@extends('layouts.app')

@section('title', __('app.teacher.chats.show.page_title', ['student' => $conversation->student_name ??
    __('app.common.unknown')]) . ' - ' . config('app.name', 'CurioGPT'))

@section('container-class', 'mx-auto max-w-3xl px-4 py-8 w-full overflow-y-auto')

@push('head')
    @vite(['resources/js/teacher-chat.js'])
@endpush

@section('content')
    <div class="w-full">

        <div class="mb-6">
            <a href="{{ route('teacher.chats.index') }}"
                class="text-xs text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
                ← {{ __('app.teacher.chats.show.back') }}
            </a>
            <div class="mt-3 flex items-center gap-3 flex-wrap">
                <h1 class="text-xl font-semibold text-black dark:text-white">
                    {{ $conversation->student_name ?? __('app.common.unknown_student') }}
                </h1>
                @if ($conversation->agent_name)
                    <span
                        class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium bg-black/5 dark:bg-white/10 text-gray-700 dark:text-gray-300">
                        {{ $conversation->agent_name }}
                    </span>
                @endif
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $conversation->title }}</p>
        </div>

        @if ($messages->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.teacher.chats.show.no_messages') }}</p>
        @else
            @php
                $userBubble = 'max-w-[80%] rounded-2xl rounded-tr-sm bg-black dark:bg-white text-white dark:text-black px-4 py-2.5
    text-sm whitespace-pre-wrap break-words leading-relaxed';
                $assistantBubble = 'flex-1 min-w-0 prose-chat text-sm text-black dark:text-white leading-relaxed';
            @endphp
            <div class="space-y-4">
                @foreach ($messages as $message)
                    @if ($message->role === 'user')
                        <div class="flex justify-end">
                            <div class="{{ $userBubble }}">{{ $message->content }}</div>
                        </div>
                    @elseif ($message->role === 'assistant')
                        <div class="flex gap-3 items-start">
                            <div class="shrink-0 mt-0.5 w-7 h-7 rounded-full bg-black dark:bg-white flex items-center justify-center"
                                aria-hidden="true">
                                <span class="text-white dark:text-black font-semibold text-xs select-none">C</span>
                            </div>
                            <div class="{{ $assistantBubble }}" data-md-source>{{ $message->content }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

    </div>

    {{-- Templates for markdown-enhanced code blocks & copy feedback --}}
    <template id="tpl-code-block">
        <div class="chat-code-block">
            <div class="chat-code-header">
                <span class="chat-code-lang" data-slot="lang"></span>
                <button class="copy-code-btn" data-code="" title="{{ __('app.common.copy_code') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                        <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                    </svg>
                    <span>{{ __('app.common.copy') }}</span>
                </button>
            </div>
            <pre class="chat-code-pre"><code class="hljs" data-slot="code"></code></pre>
        </div>
    </template>

    <template id="tpl-icon-check">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="20 6 9 17 4 12" />
        </svg>
    </template>
    <template id="tpl-copied-text">{{ __('app.common.copied') }}</template>
@endsection
