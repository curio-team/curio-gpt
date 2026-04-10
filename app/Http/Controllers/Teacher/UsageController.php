<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\ModelPricingService;
use App\Services\TokenUsageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

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

    public function exportUserInvoice(string $userId): Response
    {
        $user = DB::table('users')
            ->where('id', $userId)
            ->first(['id', 'name', 'email']);

        abort_if($user === null, 404);

        $usage = $this->usage->usageByUser((string) $user->id);

        $lineItems = [];
        $subtotalUsd = 0.0;
        $hasUnpricedItems = false;

        foreach ($usage['by_model'] as $row) {
            $estimatedCost = $this->pricing->estimateCostUsd($row['model'], $row);

            if ($estimatedCost === null) {
                $hasUnpricedItems = true;
            } else {
                $subtotalUsd += $estimatedCost;
            }

            $lineItems[] = [
                'model' => $row['model'],
                'input_tokens' => $row['input_tokens'],
                'output_tokens' => $row['output_tokens'],
                'total_tokens' => $row['total_tokens'],
                'estimated_cost_usd' => $estimatedCost,
            ];
        }

        $invoiceDate = now();
        $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.strtoupper(substr(md5((string) $user->id), 0, 6));
        $periodStart = $usage['first_usage_at'] ? Carbon::parse($usage['first_usage_at']) : null;
        $periodEnd = $usage['last_usage_at'] ? Carbon::parse($usage['last_usage_at']) : null;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('teacher.usage.invoice', [
            'user' => $user,
            'lineItems' => $lineItems,
            'usage' => $usage,
            'invoiceDate' => $invoiceDate,
            'invoiceNumber' => $invoiceNumber,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'subtotalUsd' => $subtotalUsd,
            'hasUnpricedItems' => $hasUnpricedItems,
        ])->setPaper('a4');

        return $pdf->download('usage-invoice-'.$user->id.'-'.$invoiceDate->format('Ymd').'.pdf');
    }
}
