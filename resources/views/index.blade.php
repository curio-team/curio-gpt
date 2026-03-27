@extends('layouts.app')

@section('title', __('Welcome') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
<div class="flex flex-col items-center justify-center flex-1 px-4 py-12">

    <h1 class="text-2xl font-semibold text-black dark:text-white mb-2">{{ __('Choose an agent') }}</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">{{ __('Select an agent to start chatting.') }}</p>

    @if ($agents->isEmpty())
    <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('No agents are available to you yet.') }}</p>
    @else
    <div class="flex flex-wrap justify-center gap-3 max-w-2xl w-full">
        @foreach ($agents as $agent)
        <a href="{{ route('chat.show', $agent) }}"
           class="group flex flex-col items-center gap-2 rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 p-3 text-center hover:border-black/30 dark:hover:border-white/30 hover:shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15 w-80">

            @if ($agent->image_url)
            <img src="{{ $agent->image_url }}"
                 alt="{{ $agent->name }}"
                 class="w-12 h-12 rounded-lg object-cover shrink-0">
            @else
            <div class="w-12 h-12 rounded-lg bg-black/5 dark:bg-white/10 flex items-center justify-center shrink-0">
                <span class="text-lg font-semibold text-black/40 dark:text-white/40 select-none">{{
                    strtoupper(substr($agent->name, 0, 1)) }}</span>
            </div>
            @endif

            <p class="text-xs font-medium text-black dark:text-white truncate w-full">{{ $agent->name }}</p>

            @if ($agent->description)
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-snug">{{ $agent->description }}</p>
            @endif

        </a>
        @endforeach
    </div>
    @endif

</div>
@endsection