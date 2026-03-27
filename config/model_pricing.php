<?php

return [
    // Define per-model pricing in USD per 1M tokens.
    // Keys can be exact model names or prefix patterns ending with '*'.
    // Example values below reflect the latest OpenAI pricing page as of 2026‑03‑27
    // for GPT‑5.4 series where applicable. Please verify and adjust as needed.

    'models' => [
        // Latest flagship series (example from pricing page; interpret columns as Input/Output Standard).
        // If your org uses Flex/Priority tiers, adjust rates accordingly.
        'gpt-5.4' => [
            'input_per_million' => 2.50,
            'output_per_million' => 15.00,
        ],
        'gpt-5.4-mini' => [
            'input_per_million' => 0.75,
            'output_per_million' => 4.50,
        ],
        'gpt-5.4-nano' => [
            'input_per_million' => 0.20,
            'output_per_million' => 1.25,
        ],
        'gpt-5.4-pro' => [
            'input_per_million' => 30.00,
            'output_per_million' => 180.00,
        ],

        // Legacy/common fallbacks — set these to your current contracted prices if used.
        // 'gpt-4o*' => [ 'input_per_million' => 5.00, 'output_per_million' => 15.00 ],
        // 'gpt-4o-mini*' => [ 'input_per_million' => 0.15, 'output_per_million' => 0.60 ],

        // Additional models and historical variants
        'gpt-5.2' => [
            'input_per_million' => 1.75,
            'output_per_million' => 14.00,
            'cached_input_per_million' => 0.175,
        ],
        'gpt-5.2-pro' => [
            'input_per_million' => 21.00,
            'output_per_million' => 168.00,
        ],
        'gpt-5.1' => [
            'input_per_million' => 1.25,
            'output_per_million' => 10.00,
            'cached_input_per_million' => 0.125,
        ],
        'gpt-5' => [
            'input_per_million' => 1.25,
            'output_per_million' => 10.00,
            'cached_input_per_million' => 0.125,
        ],
        'gpt-5-mini' => [
            'input_per_million' => 0.25,
            'output_per_million' => 2.00,
            'cached_input_per_million' => 0.025,
        ],
        'gpt-5-nano' => [
            'input_per_million' => 0.05,
            'output_per_million' => 0.40,
            'cached_input_per_million' => 0.005,
        ],
        'gpt-5-pro' => [
            'input_per_million' => 15.00,
            'output_per_million' => 120.00,
        ],
        'gpt-4.1' => [
            'input_per_million' => 2.00,
            'output_per_million' => 8.00,
            'cached_input_per_million' => 0.50,
        ],
        'gpt-4.1-mini' => [
            'input_per_million' => 0.40,
            'output_per_million' => 1.60,
            'cached_input_per_million' => 0.10,
        ],
        'gpt-4.1-nano' => [
            'input_per_million' => 0.10,
            'output_per_million' => 0.40,
            'cached_input_per_million' => 0.025,
        ],
        'gpt-4o' => [
            'input_per_million' => 2.50,
            'output_per_million' => 10.00,
            'cached_input_per_million' => 1.25,
        ],
        'gpt-4o-mini' => [
            'input_per_million' => 0.15,
            'output_per_million' => 0.60,
            'cached_input_per_million' => 0.075,
        ],
        'o4-mini' => [
            'input_per_million' => 1.10,
            'output_per_million' => 4.40,
            'cached_input_per_million' => 0.275,
        ],
        'o3' => [
            'input_per_million' => 2.00,
            'output_per_million' => 8.00,
            'cached_input_per_million' => 0.50,
        ],
        'o3-mini' => [
            'input_per_million' => 1.10,
            'output_per_million' => 4.40,
            'cached_input_per_million' => 0.55,
        ],
        'o3-pro' => [
            'input_per_million' => 20.00,
            'output_per_million' => 80.00,
        ],
        'o1' => [
            'input_per_million' => 15.00,
            'output_per_million' => 60.00,
            'cached_input_per_million' => 7.50,
        ],
        'o1-mini' => [
            'input_per_million' => 1.10,
            'output_per_million' => 4.40,
            'cached_input_per_million' => 0.55,
        ],
        'o1-pro' => [
            'input_per_million' => 150.00,
            'output_per_million' => 600.00,
        ],
        'gpt-4o-2024-05-13' => [
            'input_per_million' => 5.00,
            'output_per_million' => 15.00,
        ],
        'gpt-4-turbo-2024-04-09' => [
            'input_per_million' => 10.00,
            'output_per_million' => 30.00,
        ],
        'gpt-4-0125-preview' => [
            'input_per_million' => 10.00,
            'output_per_million' => 30.00,
        ],
        'gpt-4-1106-preview' => [
            'input_per_million' => 10.00,
            'output_per_million' => 30.00,
        ],
        'gpt-4-1106-vision-preview' => [
            'input_per_million' => 10.00,
            'output_per_million' => 30.00,
        ],
        'gpt-4-0613' => [
            'input_per_million' => 30.00,
            'output_per_million' => 60.00,
        ],
        'gpt-4-0314' => [
            'input_per_million' => 30.00,
            'output_per_million' => 60.00,
        ],
        'gpt-4-32k' => [
            'input_per_million' => 60.00,
            'output_per_million' => 120.00,
        ],
        'gpt-3.5-turbo' => [
            'input_per_million' => 0.50,
            'output_per_million' => 1.50,
        ],
        'gpt-3.5-turbo-0125' => [
            'input_per_million' => 0.50,
            'output_per_million' => 1.50,
        ],
        'gpt-3.5-turbo-1106' => [
            'input_per_million' => 1.00,
            'output_per_million' => 2.00,
        ],
        'gpt-3.5-turbo-0613' => [
            'input_per_million' => 1.50,
            'output_per_million' => 2.00,
        ],
        'gpt-3.5-0301' => [
            'input_per_million' => 1.50,
            'output_per_million' => 2.00,
        ],
        'gpt-3.5-turbo-instruct' => [
            'input_per_million' => 1.50,
            'output_per_million' => 2.00,
        ],
        'gpt-3.5-turbo-16k-0613' => [
            'input_per_million' => 3.00,
            'output_per_million' => 4.00,
        ],
        'davinci-002' => [
            'input_per_million' => 2.00,
            'output_per_million' => 2.00,
        ],
        'babbage-002' => [
            'input_per_million' => 0.40,
            'output_per_million' => 0.40,
        ],
    ],
];
