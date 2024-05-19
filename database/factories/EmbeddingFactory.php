<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Embedding>
 */
class EmbeddingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'file_path' => $this->faker->filePath(),
            'embedding' => json_encode($this->generateRandomEmbedding()),
        ];
    }

    private function generateRandomEmbedding(): array
    {
        return array_map(
            fn () => $this->faker->randomFloat(6, -1, 1), range(1, 768)
        );
    }
}
