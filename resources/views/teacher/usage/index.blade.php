@extends('layouts.app')

@section('title', __('app.teacher.usage.title') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
    <div class="mx-auto max-w-4xl w-full px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-semibold text-black dark:text-white">{{ __('app.teacher.usage.title') }}</h1>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @if (isset($costsOverall))
                <div class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900">
                    <div class="px-5 py-4 border-b border-black/5 dark:border-white/5">
                        <h2 class="text-sm font-medium text-black dark:text-white">
                            {{ __('app.teacher.usage.estimated_cost_overall') }}
                        </h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('app.teacher.usage.based_on_configured_pricing') }}</p>
                    </div>
                    <div class="px-5 py-4 overflow-x-auto">
                        @if (empty($costsOverall) || count($costsOverall) === 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.teacher.usage.no_usage_yet') }}
                            </p>
                        @else
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-600 dark:text-gray-300">
                                        <th class="py-2 pr-6 font-medium">{{ __('app.common.model') }}</th>
                                        <th class="py-2 pr-6 font-medium">{{ __('app.teacher.usage.input_tokens') }}</th>
                                        <th class="py-2 pr-6 font-medium">{{ __('app.teacher.usage.output_tokens') }}</th>
                                        <th class="py-2 pr-6 font-medium">{{ __('app.teacher.usage.total_tokens') }}</th>
                                        <th class="py-2 font-medium">{{ __('app.teacher.usage.est_cost_usd') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                    @foreach ($costsOverall as $row)
                                        <tr>
                                            <td class="py-2 pr-6 text-black dark:text-white">{{ $row['model'] }}</td>
                                            <td class="py-2 pr-6 text-black dark:text-white">
                                                {{ number_format($row['input_tokens']) }}
                                            </td>
                                            <td class="py-2 pr-6 text-black dark:text-white">
                                                {{ number_format($row['output_tokens']) }}
                                            </td>
                                            <td class="py-2 pr-6 text-black dark:text-white">
                                                {{ number_format($row['total_tokens']) }}
                                            </td>
                                            <td class="py-2 text-black dark:text-white">
                                                @if (is_null($row['estimated_cost_usd']))
                                                    <span
                                                        class="text-gray-500 dark:text-gray-400">{{ __('app.common.price_missing') }}</span>
                                                @else
                                                    ${{ number_format($row['estimated_cost_usd'], 4) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endif

            @if (isset($costsToday))
                <div class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900">
                    <div class="px-5 py-4 border-b border-black/5 dark:border-white/5">
                        <h2 class="text-sm font-medium text-black dark:text-white">
                            {{ __('app.teacher.usage.estimated_cost_today') }}
                        </h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('app.teacher.usage.todays_usage_only') }}</p>
                    </div>
                    <div class="px-5 py-4 overflow-x-auto">
                        @if (empty($costsToday) || count($costsToday) === 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('app.teacher.usage.no_usage_today') }}</p>
                        @else
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-600 dark:text-gray-300">
                                        <th class="py-2 pr-6 font-medium">{{ __('app.common.model') }}</th>
                                        <th class="py-2 pr-6 font-medium">{{ __('app.teacher.usage.input_tokens') }}</th>
                                        <th class="py-2 pr-6 font-medium">{{ __('app.teacher.usage.output_tokens') }}</th>
                                        <th class="py-2 pr-6 font-medium">{{ __('app.teacher.usage.total_tokens') }}</th>
                                        <th class="py-2 font-medium">{{ __('app.teacher.usage.est_cost_usd') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                    @foreach ($costsToday as $row)
                                        <tr>
                                            <td class="py-2 pr-6 text-black dark:text-white">{{ $row['model'] }}</td>
                                            <td class="py-2 pr-6 text-black dark:text-white">
                                                {{ number_format($row['input_tokens']) }}
                                            </td>
                                            <td class="py-2 pr-6 text-black dark:text-white">
                                                {{ number_format($row['output_tokens']) }}
                                            </td>
                                            <td class="py-2 pr-6 text-black dark:text-white">
                                                {{ number_format($row['total_tokens']) }}
                                            </td>
                                            <td class="py-2 text-black dark:text-white">
                                                @if (is_null($row['estimated_cost_usd']))
                                                    <span
                                                        class="text-gray-500 dark:text-gray-400">{{ __('app.common.price_missing') }}</span>
                                                @else
                                                    ${{ number_format($row['estimated_cost_usd'], 4) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endif
            <div class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900">
                <div class="px-5 py-4 border-b border-black/5 dark:border-white/5">
                    <h2 class="text-sm font-medium text-black dark:text-white">
                        {{ __('app.teacher.usage.overall_leaderboard') }}</h2>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('app.teacher.usage.top_users_total') }}
                    </p>
                </div>
                <div class="divide-y divide-black/5 dark:divide-white/5">
                    @forelse ($overall as $row)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-black dark:text-white truncate">{{ $row['name'] }}</p>
                            </div>
                            <div class="shrink-0 flex items-center gap-4">
                                <a href="{{ route('teacher.usage.invoice', ['userId' => $row['user_id']]) }}"
                                    class="inline-flex items-center rounded-lg border border-black/10 dark:border-white/10 px-3 py-1.5 text-xs font-medium text-black dark:text-white hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                                    {{ __('app.teacher.usage.export_invoice_pdf') }}
                                </a>
                                <div class="text-right">
                                <p class="text-sm font-semibold text-black dark:text-white">
                                    {{ number_format($row['total_tokens']) }}</p>
                                <p class="text-2xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                    {{ __('app.common.tokens') }}
                                </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('app.teacher.usage.no_usage_yet') }}</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900">
                <div class="px-5 py-4 border-b border-black/5 dark:border-white/5">
                    <h2 class="text-sm font-medium text-black dark:text-white">{{ __('app.teacher.usage.today') }}</h2>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('app.teacher.usage.top_users_today') }}
                    </p>
                </div>
                <div class="divide-y divide-black/5 dark:divide-white/5">
                    @forelse ($today as $row)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-black dark:text-white truncate">{{ $row['name'] }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-semibold text-black dark:text-white">
                                    {{ number_format($row['total_tokens']) }}</p>
                                <p class="text-2xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                    {{ __('app.common.tokens') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('app.teacher.usage.no_usage_yet') }}</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900">
                <div class="px-5 py-4 border-b border-black/5 dark:border-white/5">
                    <h2 class="text-sm font-medium text-black dark:text-white">
                        {{ __('app.teacher.usage.last_14_days_total') }}</h2>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('app.teacher.usage.total_tokens_per_day') }}
                    </p>
                </div>
                <div class="px-5 py-4">
                    @if ($last14->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.teacher.usage.no_recent_usage') }}
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-600 dark:text-gray-300">
                                        <th class="py-2 pr-6 font-medium">{{ __('app.common.date') }}</th>
                                        <th class="py-2 font-medium">{{ __('app.teacher.usage.total_tokens') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                                    @foreach ($last14 as $row)
                                        <tr>
                                            <td class="py-2 pr-6 text-black dark:text-white">{{ $row['date'] }}</td>
                                            <td class="py-2 text-black dark:text-white">
                                                {{ number_format($row['total_tokens']) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
