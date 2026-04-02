@extends('layouts.app')

@section('title', $agentConfig->name . ' - ' . config('app.name', 'CurioGPT'))

@section('container-class', 'flex min-h-0 flex-1 w-full')

@push('head')
    @vite(['resources/js/chat.js'])
@endpush

@section('content')

    {{-- Conversation history sidebar --}}
    @unless ($agentConfig->history_is_disabled)
        <aside id="conversations-sidebar"
            class="hidden md:flex flex-col w-60 shrink-0 border-r border-black/10 dark:border-white/10 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-black/10 dark:border-white/10 shrink-0">
                <span
                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide select-none">{{ __('app.common.history') }}</span>
                <button id="new-chat-btn" type="button" title="{{ __('app.chat.start_new_chat') }}"
                    class="text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
                    + {{ __('app.common.new') }}
                </button>
            </div>
            <div id="conversations-list" class="flex-1 overflow-y-auto py-1">
            </div>
        </aside>
    @endunless

    {{-- Chat pane --}}
    <div class="flex flex-col min-h-0 flex-1 overflow-hidden">
        <div class="flex flex-col min-h-0 flex-1 mx-auto w-full max-w-3xl" data-agent-config-id="{{ $agentConfig->id }}"
            data-history-disabled="{{ $agentConfig->history_is_disabled ? '1' : '0' }}">
            @if (!empty($agentConfig->allowed_models))
                <div class="mt-4 flex self-end gap-4 items-center justify-between">
                    <label for="model-select"
                        class="text-xs text-gray-500 dark:text-gray-400">{{ __('app.common.model') }}</label>
                    <select id="model-select" name="model"
                        class="rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-2 py-1.5 text-xs text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
                        @foreach ($agentConfig->allowed_models as $model)
                            <option value="{{ $model }}">{{ $model }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Messages feed --}}
            <div id="messages" class="flex-1 overflow-y-auto px-4 py-6 scroll-smooth">
            </div>

            {{-- Input area --}}
            <div class="shrink-0 px-4 pb-4 pt-1">
                <form id="chat-form">
                    <div
                        class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 shadow-sm focus-within:ring-2 focus-within:ring-black/10 dark:focus-within:ring-white/15 transition-shadow">
                        <label for="prompt" class="sr-only">{{ __('app.common.message') }}</label>
                        <textarea id="prompt" name="prompt" placeholder="{{ __('app.common.type_a_message') }}" rows="1"
                            class="w-full resize-none bg-transparent px-4 pt-3.5 pb-2 text-sm text-black dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none"
                            placeholder="{{ __('app.common.message') }} {{ $agentConfig->name }}…"></textarea>
                        <div class="flex items-center justify-between px-4 pb-3">
                            <span class="text-xs text-gray-500 dark:text-gray-600 select-none">
                                {{ __('app.common.enter_to_send') }} &middot; {{ __('app.common.shift_enter_new_line') }}
                            </span>
                            <div class="flex items-center gap-2">
                                <button id="cancel-edit-btn" type="button" style="display: none;"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 hover:opacity-80 transition-opacity">
                                    {{ __('app.common.cancel_edit') }}
                                </button>
                                <button id="send-btn" type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-80 disabled:opacity-30 transition-opacity">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="m22 2-7 20-4-9-9-4Z" />
                                        <path d="M22 2 11 13" />
                                    </svg>
                                    {{ __('app.common.send') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <p id="status" class="mt-1.5 text-xs text-gray-400 dark:text-gray-500 text-center h-4"></p>
                </form>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-2 text-center">
                    {{ __('app.chat.ai_disclaimer') }}
                </div>
            </div>
        </div>
    </div>

    {{-- UI templates for chat.js (for localization-friendly HTML) --}}
    <template id="tpl-user-message">
        <div class="flex justify-end mb-6 group">
            <div class="flex flex-col items-end gap-1.5 max-w-[80%]">
                <div data-slot="text"
                    class="rounded-2xl rounded-tr-sm bg-black dark:bg-white text-white dark:text-black px-4 py-2.5 text-sm whitespace-pre-wrap break-words leading-relaxed">
                </div>
                <div class="flex items-center gap-2">
                    <button
                        class="edit-msg-btn flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-0 group-hover:opacity-100"
                        data-index="" title="{{ __('app.common.edit') }}">
                        {{-- Edit icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        <span>{{ __('app.common.edit') }}</span>
                    </button>
                    <button
                        class="copy-msg-btn flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-0 group-hover:opacity-100"
                        data-text="" title="{{ __('app.common.copy') }}">
                        {{-- Copy icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                        </svg>
                        <span>{{ __('app.common.copy') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template id="tpl-assistant-message">
        <div class="flex gap-3 items-start mb-6 group">
            <div class="shrink-0 mt-0.5 w-7 h-7 rounded-full bg-black dark:bg-white flex items-center justify-center"
                aria-hidden="true">
                <span class="text-white dark:text-black font-semibold text-xs select-none">C</span>
            </div>
            <div class="flex-1 min-w-0">
                <div data-slot="content" class="prose-chat text-sm text-black dark:text-white leading-relaxed"></div>
                <div class="mt-2 h-5">
                    <button
                        class="copy-msg-btn cursor-pointer flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors opacity-0 group-hover:opacity-100"
                        data-text="" title="{{ __('app.common.copy') }}">
                        {{-- Copy icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                        </svg>
                        <span>{{ __('app.common.copy') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template id="tpl-error-message">
        <div class="flex justify-center mb-4">
            <div data-slot="text"
                class="text-xs text-red-500 dark:text-red-400 px-3 py-1.5 rounded-full bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800">
            </div>
        </div>
    </template>

    <template id="tpl-conversation-item">
        <button type="button"
            class="cursor-pointer w-full text-left px-4 py-2.5 hover:bg-black/5 dark:hover:bg-white/5 transition-colors rounded"
            data-conversation-id="">
            <p data-slot="title" class="text-xs font-medium text-black dark:text-white truncate"></p>
            <p data-slot="time" class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"></p>
        </button>
    </template>

    <template id="tpl-code-block">
        <div class="chat-code-block">
            <div class="chat-code-header">
                <span class="chat-code-lang" data-slot="lang"></span>
                <button class="copy-code-btn" data-code="" title="{{ __('app.common.copy_code') }}">
                    {{-- Copy icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">
                        <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                        <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                    </svg>
                    <span>{{ __('app.common.copy') }}</span>
                </button>
            </div>
            <pre class="chat-code-pre"><code class="hljs" data-slot="code"></code></pre>
        </div>
    </template>

    {{-- Small templates for dynamic icon/text swaps --}}
    <template id="tpl-icon-check">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="20 6 9 17 4 12" />
        </svg>
    </template>
    <template id="tpl-copied-text">{{ __('app.common.copied') }}</template>
@endsection
