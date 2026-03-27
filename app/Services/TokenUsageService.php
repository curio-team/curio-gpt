<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TokenUsageService
{
    /**
     * Returns overall token usage leaderboard for students (non-teachers).
     * Each entry: ['user_id' => string, 'name' => string, 'total_tokens' => int]
     */
    public function overallLeaderboard(int $limit = 50): Collection
    {
        $totals = [];

        DB::table('agent_conversation_messages as m')
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->whereNotNull('m.user_id')
            ->where('m.role', 'assistant')
            ->where('u.type', '!=', 'teacher')
            ->select(['m.user_id', 'u.name', 'm.usage'])
            ->orderBy('m.id')
            ->chunk(1000, function ($rows) use (&$totals) {
                foreach ($rows as $row) {
                    $usage = $this->parseUsage((string) $row->usage);
                    if ($usage['total'] <= 0) {
                        continue;
                    }
                    if (! isset($totals[$row->user_id])) {
                        $totals[$row->user_id] = [
                            'user_id' => $row->user_id,
                            'name' => $row->name,
                            'total_tokens' => 0,
                        ];
                    }
                    $totals[$row->user_id]['total_tokens'] += $usage['total'];
                }
            });

        return collect(array_values($totals))
            ->sortByDesc('total_tokens')
            ->take($limit)
            ->values();
    }

    /**
     * Returns daily token usage leaderboard for a given date (Y-m-d in app timezone).
     * Each entry: ['user_id' => string, 'name' => string, 'total_tokens' => int]
     */
    public function dailyLeaderboard(Carbon $date, int $limit = 50): Collection
    {
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $totals = [];

        DB::table('agent_conversation_messages as m')
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->whereNotNull('m.user_id')
            ->where('m.role', 'assistant')
            ->where('u.type', '!=', 'teacher')
            ->whereBetween('m.created_at', [$start, $end])
            ->select(['m.user_id', 'u.name', 'm.usage'])
            ->orderBy('m.id')
            ->chunk(1000, function ($rows) use (&$totals) {
                foreach ($rows as $row) {
                    $usage = $this->parseUsage((string) $row->usage);
                    if ($usage['total'] <= 0) {
                        continue;
                    }
                    if (! isset($totals[$row->user_id])) {
                        $totals[$row->user_id] = [
                            'user_id' => $row->user_id,
                            'name' => $row->name,
                            'total_tokens' => 0,
                        ];
                    }
                    $totals[$row->user_id]['total_tokens'] += $usage['total'];
                }
            });

        return collect(array_values($totals))
            ->sortByDesc('total_tokens')
            ->take($limit)
            ->values();
    }

    /**
     * Returns per-day totals for the past N days keyed by date string.
     * Example: ['2026-03-01' => 12345, ...]
     */
    public function dailyTotalsForAll(int $days = 14): Collection
    {
        $end = now();
        $start = now()->subDays($days - 1)->startOfDay();

        $totalsByDate = [];

        DB::table('agent_conversation_messages as m')
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->whereNotNull('m.user_id')
            ->where('m.role', 'assistant')
            ->where('u.type', '!=', 'teacher')
            ->whereBetween('m.created_at', [$start, $end])
            ->select(['m.created_at', 'm.usage'])
            ->orderBy('m.id')
            ->chunk(1000, function ($rows) use (&$totalsByDate) {
                foreach ($rows as $row) {
                    $usage = $this->parseUsage((string) $row->usage);
                    if ($usage['total'] <= 0) {
                        continue;
                    }
                    $date = (string) Carbon::parse($row->created_at)->toDateString();
                    $totalsByDate[$date] = ($totalsByDate[$date] ?? 0) + $usage['total'];
                }
            });

        // Ensure all dates in range exist, even if zero
        $dates = collect();
        for ($d = 0; $d < $days; $d++) {
            $date = now()->subDays($days - 1 - $d)->toDateString();
            $dates->push([
                'date' => $date,
                'total_tokens' => (int) ($totalsByDate[$date] ?? 0),
            ]);
        }

        // Sort by date descending, so the top show the most recent day first
        $dates = $dates->sortByDesc('date')->values();

        return $dates;
    }

    /**
     * Aggregate token usage by model name across all time (students only, assistant messages).
     * Returns collection of [model => ['input_tokens'=>int,'output_tokens'=>int,'total_tokens'=>int]].
     */
    public function tokensByModelOverall(): Collection
    {
        $totals = [];

        DB::table('agent_conversation_messages as m')
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->whereNotNull('m.user_id')
            ->where('m.role', 'assistant')
            ->where('u.type', '!=', 'teacher')
            ->select(['m.usage', 'm.meta', 'm.agent'])
            ->orderBy('m.id')
            ->chunk(1000, function ($rows) use (&$totals) {
                foreach ($rows as $row) {
                    $usage = $this->parseUsage((string) $row->usage);
                    if ($usage['total'] <= 0) {
                        continue;
                    }
                    $model = $this->parseModel((string) $row->usage, (string) $row->meta, (string) $row->agent);
                    if (! isset($totals[$model])) {
                        $totals[$model] = [
                            'input_tokens' => 0,
                            'output_tokens' => 0,
                            'total_tokens' => 0,
                        ];
                    }
                    $totals[$model]['input_tokens'] += $usage['input'];
                    $totals[$model]['output_tokens'] += $usage['output'];
                    $totals[$model]['total_tokens'] += $usage['total'];
                }
            });

        return collect($totals)->sortByDesc('total_tokens');
    }

    /**
     * Aggregate token usage by model for a specific date (students only, assistant messages).
     */
    public function tokensByModelForDate(Carbon $date): Collection
    {
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $totals = [];

        DB::table('agent_conversation_messages as m')
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->whereNotNull('m.user_id')
            ->where('m.role', 'assistant')
            ->where('u.type', '!=', 'teacher')
            ->whereBetween('m.created_at', [$start, $end])
            ->select(['m.usage', 'm.meta', 'm.agent'])
            ->orderBy('m.id')
            ->chunk(1000, function ($rows) use (&$totals) {
                foreach ($rows as $row) {
                    $usage = $this->parseUsage((string) $row->usage);
                    if ($usage['total'] <= 0) {
                        continue;
                    }
                    $model = $this->parseModel((string) $row->usage, (string) $row->meta, (string) $row->agent);
                    if (! isset($totals[$model])) {
                        $totals[$model] = [
                            'input_tokens' => 0,
                            'output_tokens' => 0,
                            'total_tokens' => 0,
                        ];
                    }
                    $totals[$model]['input_tokens'] += $usage['input'];
                    $totals[$model]['output_tokens'] += $usage['output'];
                    $totals[$model]['total_tokens'] += $usage['total'];
                }
            });

        return collect($totals)->sortByDesc('total_tokens');
    }

    /**
     * Robustly parse usage JSON into token counts.
     * Returns ['input' => int, 'output' => int, 'total' => int]
     */
    private function parseUsage(string $usageText): array
    {
        $input = 0;
        $output = 0;
        $total = 0;

        $data = json_decode($usageText, true);
        if (! is_array($data)) {
            return compact('input', 'output', 'total');
        }

        // Common keys across providers
        $candidates = [
            'prompt_tokens',
            'input_tokens',
            'inputTokens',
            'promptTokens',
        ];
        foreach ($candidates as $k) {
            if (isset($data[$k]) && is_numeric($data[$k])) {
                $input = (int) $data[$k];
                break;
            }
        }

        $candidates = [
            'completion_tokens',
            'output_tokens',
            'outputTokens',
            'completionTokens',
        ];
        foreach ($candidates as $k) {
            if (isset($data[$k]) && is_numeric($data[$k])) {
                $output = (int) $data[$k];
                break;
            }
        }

        $candidates = [
            'total_tokens',
            'totalTokens',
            'total',
        ];
        foreach ($candidates as $k) {
            if (isset($data[$k]) && is_numeric($data[$k])) {
                $total = (int) $data[$k];
                break;
            }
        }

        if ($total === 0) {
            $total = $input + $output;
        }

        return [
            'input' => $input,
            'output' => $output,
            'total' => $total,
        ];
    }

    /**
     * Try to extract model name from usage/meta JSON blobs or fallback to agent string.
     */
    private function parseModel(string $usageText, string $metaText, string $agent): string
    {
        $candidates = [];

        $u = json_decode($usageText, true) ?: [];
        if (is_array($u)) {
            foreach (['model', 'model_name', 'modelName'] as $k) {
                if (! empty($u[$k]) && is_string($u[$k])) {
                    $candidates[] = $u[$k];
                }
            }
        }

        $m = json_decode($metaText, true) ?: [];
        if (is_array($m)) {
            foreach (['model', 'provider_model', 'api.model'] as $k) {
                // dotted key support
                $val = $m[$k] ?? ($m['api']['model'] ?? null);
                if (! empty($val) && is_string($val)) {
                    $candidates[] = $val;
                }
            }
        }

        foreach ($candidates as $c) {
            $c = trim($c);
            if ($c !== '') {
                return $c;
            }
        }

        // Fallback: sometimes agent string contains model or agent identifier
        if ($agent !== '') {
            return $agent;
        }

        return 'unknown';
    }
}
