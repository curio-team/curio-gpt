@extends('layouts.app')

@section('title', __('Edit Agent') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
<div class="mx-auto max-w-2xl w-full px-4 py-8">

  <div class="mb-6">
    <a href="{{ route('teacher.agents.index') }}"
       class="text-xs text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
      ← {{ __('Back to Agents') }}
    </a>
    <h1 class="mt-3 text-xl font-semibold text-black dark:text-white">{{ __('Edit Agent') }}</h1>
  </div>

  <form method="POST"
        action="{{ route('teacher.agents.update', $agentConfig) }}">
    @csrf
    @method('PUT')

    <div
         class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 divide-y divide-black/5 dark:divide-white/5">

      <div class="px-5 py-4">
        <label for="name"
               class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
          {{ __('Name') }}
        </label>
        <input id="name"
               name="name"
               type="text"
               value="{{ old('name', $agentConfig->name) }}"
               class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15"
               required
               maxlength="100">
        @error('name')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
      </div>

      <div class="px-5 py-4">
        <label for="instructions"
               class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
          {{ __('Instructions') }}
        </label>
        <textarea id="instructions"
                  name="instructions"
                  rows="8"
                  class="w-full rounded-lg border border-black/10 dark:border-white/10 bg-transparent px-3 py-2 text-sm text-black dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black/10 dark:focus:ring-white/15 resize-y"
                  required
                  maxlength="10000">{{ old('instructions', $agentConfig->instructions) }}</textarea>
        @error('instructions')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
      </div>

    </div>

    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-80 transition-opacity">
        {{ __('Save Changes') }}
      </button>
    </div>
  </form>

</div>
@endsection