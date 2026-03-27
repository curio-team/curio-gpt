@extends('layouts.app')

@section('title', ($errorCode ?? '') . ' – ' . ($title ?? __('Error')) . ' - ' . config('app.name', 'CurioGPT'))

@section('content')

@php
$code = $errorCode ?? '500';
$heading = $title ?? __('Something went wrong');
$body = $message ?? __('An unexpected error occurred. Please try again later.');
$url = $redirectUrl ?? '/';
$label = $redirectLabel ?? __('Go back home');
@endphp

<div class="flex flex-col items-center justify-center flex-1 px-4 py-12">

    {{-- Ghost error code in the background --}}
    <p class="text-xl sm:text-[9rem] font-semibold leading-none
               text-red-600
               select-none tabular-nums mb-2"
       aria-hidden="true">
        {{ $code }}
    </p>

    {{-- Title --}}
    <h1 class="text-2xl font-semibold text-black dark:text-white mb-2 text-center">
        {{ $heading }}
    </h1>

    {{-- Helpful message --}}
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 text-center max-w-sm leading-relaxed">
        {{ $body }}
    </p>

    {{-- Redirect CTA – styled like the agent cards --}}
    <a href="{{ $url }}"
       class="group flex items-center gap-3 rounded-xl border border-black/10 dark:border-white/10
              bg-white dark:bg-neutral-900 px-5 py-3
              hover:border-black/30 dark:hover:border-white/30 hover:shadow-sm
              transition-all focus:outline-none focus:ring-2
              focus:ring-black/10 dark:focus:ring-white/15">

        {{-- Arrow icon --}}
        <span class="w-8 h-8 rounded-lg bg-black/5 dark:bg-white/10
                     flex items-center justify-center shrink-0
                     group-hover:bg-black/10 dark:group-hover:bg-white/15 transition-colors">
            <svg class="w-4 h-4 text-black/50 dark:text-white/50
                        group-hover:-translate-x-0.5 transition-transform"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke-width="2"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </span>

        <span class="text-xs font-medium text-black dark:text-white">
            {{ $label }}
        </span>
    </a>

</div>

@endsection