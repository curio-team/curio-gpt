<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAiModelService
{
    /**
     * Fetch a list of available OpenAI model IDs suitable for chat.
     * Falls back to a curated list if the API request fails.
     *
     * @return array<int, string>
     */
    public function listChatModels(): array
    {
        $url = rtrim(config('ai.providers.openai.url', 'https://api.openai.com/v1'), '/');
        $key = config('ai.providers.openai.key');

        if (filled($key)) {
            try {
                $response = Http::baseUrl($url)
                    ->withToken($key)
                    ->timeout(15)
                    ->acceptJson()
                    ->get('models');

                if ($response->ok()) {
                    $ids = collect($response->json('data', []))
                        ->pluck('id')
                        ->filter(fn ($id) => is_string($id))
                        // Rough filter to interactive chat-capable models
                        ->filter(fn (string $id) => str_starts_with($id, 'gpt-') || str_starts_with($id, 'o'))
                        ->unique()
                        ->values()
                        ->all();

                    if (! empty($ids)) {
                        // Prefer commonly used models near the top if present
                        $priority = ['gpt-4o', 'gpt-4o-mini', 'gpt-4.1', 'o4-mini', 'o3-mini'];
                        usort($ids, function ($a, $b) use ($priority) {
                            $pa = array_search($a, $priority, true);
                            $pb = array_search($b, $priority, true);
                            if ($pa === false && $pb === false) {
                                return strcmp($a, $b);
                            }
                            if ($pa === false) {
                                return 1;
                            }
                            if ($pb === false) {
                                return -1;
                            }

                            return $pa <=> $pb;
                        });

                        return $ids;
                    }
                }
            } catch (\Throwable $e) {
                // Swallow and fall back below
            }
        }

        // Fallback curated list
        return [
            'gpt-4o',
            'gpt-4o-mini',
            'gpt-4.1',
            'o4-mini',
            'o3-mini',
        ];
    }
}
