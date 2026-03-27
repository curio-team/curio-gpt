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
        ];
    }
}
