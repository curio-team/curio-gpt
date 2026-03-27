<?php

namespace Database\Factories;

use App\Models\AgentConfig;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgentConfig>
 */
class AgentConfigFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'instructions' => $this->faker->paragraph(),
            'created_by' => User::factory(),
            'allowed_models' => null,
            'is_enabled' => true,
            'available_from' => null,
            'available_until' => null,
            'monitoring_is_enabled' => false,
            'monitoring_instructions' => null,
            'monitoring_model' => null,
        ];
    }

    public function disabled(): static
    {
        return $this->state(['is_enabled' => false]);
    }

    public function availableBetween(string $from, string $until): static
    {
        return $this->state([
            'is_enabled' => true,
            'available_from' => $from,
            'available_until' => $until,
        ]);
    }
}
