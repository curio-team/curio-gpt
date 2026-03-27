@extends('layouts.app')

@section('title', __('Token Usage') . ' - ' . config('app.name', 'CurioGPT'))

@section('content')
<div class="mx-auto max-w-4xl w-full px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-black dark:text-white">{{ __('Token Usage') }}</h1>
    </div>

    <div class="grid grid-cols-1 gap-6">
        @if (isset($costsOverall))
        <div class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900">
            <div class="px-5 py-4 border-b border-black/5 dark:border-white/5">
                <h2 class="text-sm font-medium text-black dark:text-white">{{ __('Estimated Cost by Model (Overall)') }}
                </h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Based on configured per-model token
                    pricing') }}</p>
            </div>
            <div class="px-5 py-4 overflow-x-auto">
                @if (empty($costsOverall) || count($costsOverall) === 0)
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No usage yet.') }}</p>
                @else
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="py-2 pr-6 font-medium">{{ __('Model') }}</th>
                            <th class="py-2 pr-6 font-medium">{{ __('Input tokens') }}</th>
                            <th class="py-2 pr-6 font-medium">{{ __('Output tokens') }}</th>
                            <th class="py-2 pr-6 font-medium">{{ __('Total tokens') }}</th>
                            <th class="py-2 font-medium">{{ __('Est. cost (USD)') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @foreach ($costsOverall as $row)
                        <tr>
                            <td class="py-2 pr-6 text-black dark:text-white">{{ $row['model'] }}</td>
                            <td class="py-2 pr-6 text-black dark:text-white">{{ number_format($row['input_tokens']) }}
                            </td>
                            <td class="py-2 pr-6 text-black dark:text-white">{{ number_format($row['output_tokens']) }}
                            </td>
                            <td class="py-2 pr-6 text-black dark:text-white">{{ number_format($row['total_tokens']) }}
                            </td>
                            <td class="py-2 text-black dark:text-white">
                                @if (is_null($row['estimated_cost_usd']))
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Price missing') }}</span>
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
                <h2 class="text-sm font-medium text-black dark:text-white">{{ __('Estimated Cost by Model (Today)') }}
                </h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Today\'s usage only') }}</p>
            </div>
            <div class="px-5 py-4 overflow-x-auto">
                @if (empty($costsToday) || count($costsToday) === 0)
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No usage today.') }}</p>
                @else
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="py-2 pr-6 font-medium">{{ __('Model') }}</th>
                            <th class="py-2 pr-6 font-medium">{{ __('Input tokens') }}</th>
                            <th class="py-2 pr-6 font-medium">{{ __('Output tokens') }}</th>
                            <th class="py-2 pr-6 font-medium">{{ __('Total tokens') }}</th>
                            <th class="py-2 font-medium">{{ __('Est. cost (USD)') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @foreach ($costsToday as $row)
                        <tr>
                            <td class="py-2 pr-6 text-black dark:text-white">{{ $row['model'] }}</td>
                            <td class="py-2 pr-6 text-black dark:text-white">{{ number_format($row['input_tokens']) }}
                            </td>
                            <td class="py-2 pr-6 text-black dark:text-white">{{ number_format($row['output_tokens']) }}
                            </td>
                            <td class="py-2 pr-6 text-black dark:text-white">{{ number_format($row['total_tokens']) }}
                            </td>
                            <td class="py-2 text-black dark:text-white">
                                @if (is_null($row['estimated_cost_usd']))
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Price missing') }}</span>
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
                <h2 class="text-sm font-medium text-black dark:text-white">{{ __('Overall Leaderboard') }}</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Top students by total tokens used') }}
                </p>
            </div>
            <div class="divide-y divide-black/5 dark:divide-white/5">
                @forelse ($overall as $row)
                <div class="flex items-center justify-between px-5 py-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-black dark:text-white truncate">{{ $row['name'] }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-semibold text-black dark:text-white">{{
                            number_format($row['total_tokens']) }}</p>
                        <p class="text-2xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('tokens') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-5 py-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No usage yet.') }}</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900">
            <div class="px-5 py-4 border-b border-black/5 dark:border-white/5">
                <h2 class="text-sm font-medium text-black dark:text-white">{{ __('Today') }}</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Top students by tokens used today') }}
                </p>
            </div>
            <div class="divide-y divide-black/5 dark:divide-white/5">
                @forelse ($today as $row)
                <div class="flex items-center justify-between px-5 py-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-black dark:text-white truncate">{{ $row['name'] }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-semibold text-black dark:text-white">{{
                            number_format($row['total_tokens']) }}</p>
                        <p class="text-2xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('tokens') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="px-5 py-6 text-sm text-gray-500 dark:text-gray-400">{{ __('No usage today.') }}</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-black/10 dark:border-white/10 bg-white dark:bg-neutral-900">
            <div class="px-5 py-4 border-b border-black/5 dark:border-white/5">
                <h2 class="text-sm font-medium text-black dark:text-white">{{ __('Last 14 Days (Total)') }}</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Total tokens used per day by all
                    students') }}
                </p>
            </div>
            <div class="px-5 py-4">
                @if ($last14->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No recent usage.') }}</p>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="py-2 pr-6 font-medium">{{ __('Date') }}</th>
                                <th class="py-2 font-medium">{{ __('Total tokens') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 dark:divide-white/5">
                            @foreach ($last14 as $row)
                            <tr>
                                <td class="py-2 pr-6 text-black dark:text-white">{{ $row['date'] }}</td>
                                <td class="py-2 text-black dark:text-white">{{ number_format($row['total_tokens']) }}
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