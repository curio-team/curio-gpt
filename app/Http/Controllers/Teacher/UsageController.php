<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\ModelPricingService;
use App\Services\TokenUsageService;
use Carbon\Carbon;
use Illuminate\View\View;

class UsageController extends Controller
{
    public function __construct(
        private readonly TokenUsageService $usage,
        private readonly ModelPricingService $pricing,
    ) {}

    public function index(): View
    {
        $overall = $this->usage->overallLeaderboard(100);
        $today = $this->usage->dailyLeaderboard(Carbon::today(), 50);
        $last14 = $this->usage->dailyTotalsForAll(14);

        $byModelOverall = $this->usage->tokensByModelOverall();
        $byModelToday = $this->usage->tokensByModelForDate(Carbon::today());

        $costsOverall = [];
        foreach ($byModelOverall as $model => $tokens) {
            $costsOverall[] = [
                'model' => $model,
                'input_tokens' => $tokens['input_tokens'],
                'output_tokens' => $tokens['output_tokens'],
                'total_tokens' => $tokens['total_tokens'],
                'estimated_cost_usd' => $this->pricing->estimateCostUsd($model, $tokens),
            ];
        }

        $costsToday = [];
        foreach ($byModelToday as $model => $tokens) {
            $costsToday[] = [
                'model' => $model,
                'input_tokens' => $tokens['input_tokens'],
                'output_tokens' => $tokens['output_tokens'],
                'total_tokens' => $tokens['total_tokens'],
                'estimated_cost_usd' => $this->pricing->estimateCostUsd($model, $tokens),
            ];
        }

        return view('teacher.usage.index', [
            'overall' => $overall,
            'today' => $today,
            'last14' => $last14,
            'costsOverall' => collect($costsOverall)->sortByDesc('total_tokens')->values(),
            'costsToday' => collect($costsToday)->sortByDesc('total_tokens')->values(),
        ]);
    }
}
