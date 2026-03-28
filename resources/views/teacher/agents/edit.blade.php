@extends('layouts.app')

@section('title', __('app.teacher.agents.edit.title') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
    <div class="mx-auto max-w-2xl w-full px-4 py-8">

        <div class="mb-6">
            <a href="{{ route('teacher.agents.index') }}"
                class="text-xs text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">
                ← {{ __('app.teacher.agents.edit.back') }}
            </a>
            <h1 class="mt-3 text-xl font-semibold text-black dark:text-white">{{ __('app.teacher.agents.edit.title') }}</h1>
        </div>

        <form method="POST" action="{{ route('teacher.agents.update', $agent) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div
                class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900 divide-y divide-black/5 dark:divide-white/5">
                @include('teacher.agents.form')
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium bg-black text-white dark:bg-white dark:text-black hover:opacity-80 transition-opacity">
                    {{ __('app.teacher.agents.edit.submit') }}
                </button>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <form id="agent-attachments-upload" method="POST" action="{{ route('teacher.agents.attachments.store', $agent) }}"
        enctype="multipart/form-data" class="hidden">
        @csrf
    </form>
    @php $__attachments = $agent->attachments ?? []; @endphp
    @foreach ($__attachments as $__att)
        <form id="agent-attachment-delete-{{ $__att['id'] ?? '' }}" method="POST"
            action="{{ route('teacher.agents.attachments.destroy', [$agent, $__att['id'] ?? '']) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endpush
