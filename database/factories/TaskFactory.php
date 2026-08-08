<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'is_completed' => false,
            'is_important' => false,
            'due_date' => null,
            'note' => null,
            'color' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => ['is_completed' => true]);
    }

    public function important(): static
    {
        return $this->state(fn () => ['is_important' => true]);
    }

    public function dueToday(): static
    {
        return $this->state(fn () => ['due_date' => now()->toDateString()]);
    }
}
