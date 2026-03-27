@extends('layouts.app')

@section('title', __('Welcome') . ' - ' . config('app.name', 'CurioGPT'))

@section('container-class', 'flex flex-col min-h-0 flex-1 w-full')

@push('head')
@vite(['resources/js/chat.js'])
@endpush

@section('content')
<div class="flex flex-col min-h-0 flex-1 mx-auto w-full max-w-3xl">

    {{-- Messages feed --}}
    <div id="messages"
         class="flex-1 overflow-y-auto px-4 py-6 scroll-smooth">

        {{-- Empty / welcome state --}}
        <div id="empty-state"
             class="flex flex-col items-center justify-center h-full gap-5 text-center px-4 min-h-64">
            <div class="w-14 h-14 rounded-full bg-black dark:bg-white flex items-center justify-center shadow-md">
                <span class="text-white dark:text-black text-2xl font-bold select-none">C</span>
            </div>
            <div>
                <h1 class="text-xl font-semibold text-black dark:text-white">{{ __('How can I help you?') }}</h1>
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ __('Start a conversation with CurioGPT')
                    }}</p>
            </div>
            @guest
            <p
               class="text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-full px-3 py-1.5">
                {{ __('You must be logged in to chat with the agent.') }}
            </p>
            @endguest
        </div>

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
                          placeholder="{{ __('Message CurioGPT…') }}"></textarea>
                <div class="flex items-center justify-between px-4 pb-3">
                    <span class="text-xs text-gray-500 dark:text-gray-600 select-none">
                        {{ __('Enter to send') }} &middot; {{ __('Shift+Enter for new line') }}
                    </span>
                    <button id="send-btn"
                            type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-80 disabled:opacity-30 transition-opacity">
                        {{ __('Send') }}
                    </button>
                </div>
            </div>
            <p id="status"
               class="mt-1.5 text-xs text-gray-400 dark:text-gray-500 text-center h-4"></p>
        </form>
    </div>

</div>
@endsection