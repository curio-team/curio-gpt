<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->regexify('[A-Z]{2}[0-9]{2}'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'type' => fake()->randomElement(['teacher', 'student']),
            'remember_token' => Str::random(10),
        ];
    }
}
