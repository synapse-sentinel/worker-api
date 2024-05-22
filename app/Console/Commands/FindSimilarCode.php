<?php

namespace App\Console\Commands;

use App\Models\Embedding;
use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

class FindSimilarCode extends Command
{
    protected $signature = 'code:find-similar {prompt}';

    protected $description = 'Find similar code snippets based on a user-provided prompt';

    public function handle()
    {

        // Prompt the user for input
        $prompt = $this->argument('prompt') ?? $this->ask('Enter a description of what you are looking for in the codebase');

        // Generate embedding for the user prompt
        $promptEmbedding = $this->generatePromptEmbedding($prompt);

        // Fetch relevant embeddings
        $embeddings = Embedding::where('embedding', '!=', null)->get();
        // Calculate similarity scores
        $similarities = [];
        foreach ($embeddings as $embedding) {
            $storedEmbedding = json_decode($embedding->embedding, true);
            if (! is_array($storedEmbedding)) {

                continue;
            }
            $similarity = $this->cosineSimilarity($promptEmbedding, $storedEmbedding);
            $similarities[$embedding->file_path] = $similarity;
        }

        // Sort by similarity
        arsort($similarities);

        // Get top results
        return array_slice(array_keys($similarities), 0, 5);
    }

    protected function generatePromptEmbedding($prompt)
    {
        $response = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $prompt,
        ]);

        return $response['data'][0]['embedding'];
    }

    protected function cosineSimilarity(array $vecA, array $vecB)
    {
        $dotProduct = array_sum(array_map(function ($a, $b) {
            return $a * $b;
        }, $vecA, $vecB));
        $magnitudeA = sqrt(array_sum(array_map(function ($a) {
            return $a * $a;
        }, $vecA)));
        $magnitudeB = sqrt(array_sum(array_map(function ($b) {
            return $b * $b;
        }, $vecB)));

        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0; // Avoid division by zero
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }
}
