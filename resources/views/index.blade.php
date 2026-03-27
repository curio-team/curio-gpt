<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>{{ __('Welcome') }} - {{ config('app.name', 'Laravel') }}</title>

    <link rel="icon"
          href="/favicon.ico"
          sizes="any">
    <link rel="icon"
          href="/favicon.svg"
          type="image/svg+xml">
    <link rel="apple-touch-icon"
          href="/apple-touch-icon.png">

    <link rel="preconnect"
          href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
          rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
      class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    <div
         class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
            <div class="flex flex-col">
                <h1 class="text-3xl font-bold tracking-tight text-center lg:text-left lg:text-5xl text-white">
                    {{ __('Welcome to CurioGPT') }}
                </h1>
                <h2 class="text-lg leading-8 text-center lg:text-left lg:text-xl text-gray-600 dark:text-gray-400">
                    {{ __('This is a work in progress, come back later!') }}
                </h2>
            </div>
        </main>
    </div>
</body>

</html>