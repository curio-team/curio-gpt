@extends('layouts.app')

@section('title', __('app.teacher.chats.show.page_title', ['student' => $conversation->student_name ??
__('app.common.unknown')]) . ' - ' .
config('app.name', 'CurioGPT'))

@section('container-class', 'mx-auto max-w-3xl px-4 py-8 w-full overflow-y-auto')

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
    $assistantBubble = 'flex-1 min-w-0 text-sm text-black dark:text-white leading-relaxed whitespace-pre-wrap
    break-words';
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
            <div class="{{ $assistantBubble }}">{{ $message->content }}</div>
        </div>
        @endif
        @endforeach
    </div>
    @endif

</div>
@endsection