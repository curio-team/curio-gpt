<?php

namespace App\Services;

class ModelPricingService
{
    /**
     * Get pricing rates for a given model name.
     * Returns ['input_per_million' => float, 'output_per_million' => float] in USD,
     * or null if unknown.
     */
    public function getRates(string $model): ?array
    {
        $models = (array) config('model_pricing.models', []);

        // Exact match
        if (isset($models[$model])) {
            return $this->normalize($models[$model]);
        }

        // Prefix/regex patterns
        foreach ($models as $key => $value) {
            if (str_ends_with($key, '*')) {
                $prefix = substr($key, 0, -1);
                if (str_starts_with($model, $prefix)) {
                    return $this->normalize($value);
                }
            }
        }

        return null;
    }

    /**
     * Estimate cost for a token breakdown using the provided model name.
     * $tokens = ['input_tokens' => int, 'output_tokens' => int].
     */
    public function estimateCostUsd(string $model, array $tokens): ?float
    {
        $rates = $this->getRates($model);
        if ($rates === null) {
            return null;
        }

        $input = (int) ($tokens['input_tokens'] ?? 0);
        $cachedInput = (int) ($tokens['cached_input_tokens'] ?? 0);
        $output = (int) ($tokens['output_tokens'] ?? 0);

        $standardInput = max(0, $input - $cachedInput);

        $inputStandardCost = ($standardInput / 1_000_000) * (float) $rates['input_per_million'];
        $inputCachedCost = 0.0;
        if (! empty($rates['cached_input_per_million']) && $cachedInput > 0) {
            $inputCachedCost = ($cachedInput / 1_000_000) * (float) $rates['cached_input_per_million'];
        } elseif ($cachedInput > 0) {
            // fallback: if no cached price defined, bill cached as standard
            $inputCachedCost = ($cachedInput / 1_000_000) * (float) $rates['input_per_million'];
        }

        $outputCost = ($output / 1_000_000) * (float) $rates['output_per_million'];

        $cost = $inputStandardCost + $inputCachedCost + $outputCost;

        return round($cost, 6);
    }

    private function normalize(array $value): array
    {
        return [
            'input_per_million' => (float) ($value['input_per_million'] ?? 0.0),
            'output_per_million' => (float) ($value['output_per_million'] ?? 0.0),
            'cached_input_per_million' => isset($value['cached_input_per_million'])
                ? (float) $value['cached_input_per_million']
                : null,
        ];
    }
}
