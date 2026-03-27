<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'CurioGPT'))</title>

    {{--
    <link rel="icon"
          href="/favicon.ico"
          sizes="any">
    <link rel="icon"
          href="/favicon.svg"
          type="image/svg+xml">
    <link rel="apple-touch-icon"
          href="/apple-touch-icon.png">
    --}}

    <link rel="preconnect"
          href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
          rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] h-dvh flex flex-col overflow-hidden">
    <header class="border-b border-black/5 dark:border-white/10">
        <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}"
               class="font-semibold tracking-tight text-lg text-black dark:text-white">CurioGPT</a>
            <nav class="flex items-center gap-3">
                @auth
                @if (auth()->user()->isTeacher())
                <a href="{{ route('teacher.agents.index') }}"
                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white transition-colors">
                    {{ __('Manage Agents') }}
                </a>
                <span class="text-gray-300 dark:text-gray-700 select-none">·</span>
                <a href="{{ route('teacher.chats.index') }}"
                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white transition-colors">
                    {{ __('Chats') }}
                </a>
                <span class="text-gray-300 dark:text-gray-700 select-none">·</span>
                @endif
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ auth()->user()->name }}</span>
                @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-90">
                    {{ __('Log in') }}
                </a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1 flex flex-col min-h-0 overflow-hidden">
        <div class="flex-1 min-h-0 @yield('container-class', 'mx-auto max-w-6xl px-4 py-6 w-full overflow-y-auto')">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>

</html>