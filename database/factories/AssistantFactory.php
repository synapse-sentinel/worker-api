<?php

namespace Database\Factories;

use App\Models\AiModel;
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
            'ai_model_id' => AiModel::factory()->create(),
            'instructions' => $this->faker->text,
        ];
    }
}
