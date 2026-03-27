@extends('layouts.app')

@section('title', $agentConfig->name . ' - ' . config('app.name', 'CurioGPT'))

@section('container-class', 'flex min-h-0 flex-1 w-full')

@push('head')
@vite(['resources/js/chat.js'])
@endpush

@section('content')

{{-- Conversation history sidebar --}}
<aside id="conversations-sidebar"
       class="hidden md:flex flex-col w-60 shrink-0 border-r border-black/10 dark:border-white/10 overflow-hidden">
    <div class="flex items-center justify-between px-4 py-3 border-b border-black/10 dark:border-white/10 shrink-0">
        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide select-none">{{
            __('History') }}</span>
        <button id="new-chat-btn"
                type="button"
                title="{{ __('Start a new chat') }}"
                class="text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
            + {{ __('New') }}
        </button>
    </div>
    <div id="conversations-list"
         class="flex-1 overflow-y-auto py-1">
    </div>
</aside>

{{-- Chat pane --}}
<div class="flex flex-col min-h-0 flex-1 overflow-hidden">
    <div class="flex flex-col min-h-0 flex-1 mx-auto w-full max-w-3xl"
         data-agent-config-id="{{ $agentConfig->id }}">
        @if (!empty($agentConfig->allowed_models))
        <div class="mt-4 flex self-end gap-4 items-center justify-between">
            <label for="model-select"
                   class="text-xs text-gray-500 dark:text-gray-400">{{ __('Model') }}</label>
            <select id="model-select"
                    name="model"
                    class="rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-2 py-1.5 text-xs text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15">
                @foreach ($agentConfig->allowed_models as $model)
                <option value="{{ $model }}">{{ $model }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Messages feed --}}
        <div id="messages"
             class="flex-1 overflow-y-auto px-4 py-6 scroll-smooth">
        </div>

        {{-- Input area --}}
        <div class="shrink-0 px-4 pb-4 pt-1">
            <form id="chat-form">
                <div
                     class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 shadow-sm focus-within:ring-2 focus-within:ring-black/10 dark:focus-within:ring-white/15 transition-shadow">
                    <label for="prompt"
                           class="sr-only">{{ __('Message') }}</label>
                    <textarea id="prompt"
                              name="prompt"
                              rows="1"
                              class="w-full resize-none bg-transparent px-4 pt-3.5 pb-2 text-sm text-black dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none"
                              placeholder="{{ __('Message') }} {{ $agentConfig->name }}…"></textarea>
                    <div class="flex items-center justify-between px-4 pb-3">
                        <span class="text-xs text-gray-500 dark:text-gray-600 select-none">
                            {{ __('Enter to send') }} &middot; {{ __('Shift+Enter for new line') }}
                        </span>
                        <div class="flex items-center gap-2">
                            <button id="cancel-edit-btn"
                                    type="button"
                                    style="display: none;"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 hover:opacity-80 transition-opacity">
                                {{ __('Cancel edit') }}
                            </button>
                            <button id="send-btn"
                                    type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-80 disabled:opacity-30 transition-opacity">
                                {{ __('Send') }}
                            </button>
                        </div>
                    </div>
                </div>
                <p id="status"
                   class="mt-1.5 text-xs text-gray-400 dark:text-gray-500 text-center h-4"></p>
            </form>
        </div>

    </div>
</div>
@endsection