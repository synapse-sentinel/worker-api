<?php

namespace Database\Factories;

use App\Models\Assistant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assistant>
 */
class AssistantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
