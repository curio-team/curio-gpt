@extends('layouts.app')

@section('title', __('Manage Agents') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
<div class="mx-auto max-w-3xl w-full px-4 py-8">

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-black dark:text-white">{{ __('Agents') }}</h1>
    <a href="{{ route('teacher.agents.create') }}"
       class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-80 transition-opacity">
      {{ __('New Agent') }}
    </a>
  </div>

  @if (session('success'))
  <div
       class="mb-4 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/30 px-4 py-2.5 text-sm text-green-800 dark:text-green-300">
    {{ session('success') }}
  </div>
  @endif

  @if ($agents->isEmpty())
  <div
       class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 px-6 py-12 text-center">
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No agents yet. Create one to get started.') }}</p>
  </div>
  @else
  <div
       class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 divide-y divide-black/5 dark:divide-white/5">
    @foreach ($agents as $agent)
    <div class="flex items-start justify-between gap-4 px-5 py-4">
      <div class="min-w-0 flex-1">
        <p class="text-sm font-medium text-black dark:text-white truncate">{{ $agent->name }}</p>
        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $agent->instructions }}</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <a href="{{ route('teacher.agents.edit', $agent) }}"
           class="text-xs text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
          {{ __('Edit') }}
        </a>
        <form method="POST"
              action="{{ route('teacher.agents.destroy', $agent) }}"
              onsubmit="return confirm('{{ __('Delete this agent?') }}')">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="text-xs text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors">
            {{ __('Delete') }}
          </button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
  @endif

  <div class="mt-8">
    <a href="{{ route('teacher.chats.index') }}"
       class="text-sm text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
      {{ __('→ View all student chats') }}
    </a>
  </div>

</div>
@endsection