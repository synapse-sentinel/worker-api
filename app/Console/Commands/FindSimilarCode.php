<?php

namespace App\Console\Commands;

use App\Models\Context;
use App\Models\Embedding;
use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

class FindSimilarCode extends Command
{
    protected $signature = 'code:find-similar';

    protected $description = 'Find similar code snippets based on a user-provided prompt';

    public function handle()
    {
        // Prompt the user for input
        $prompt = $this->ask('Enter a description of what you are looking for in the codebase');

        // Generate embedding for the user prompt
        $promptEmbedding = $this->generatePromptEmbedding($prompt);

        // Fetch relevant embeddings
        $contextTags = $this->ask('Enter relevant context tags (comma separated)', '');
        $contextTags = array_map('trim', explode(',', $contextTags));
        $embeddings = $this->fetchRelevantEmbeddings($contextTags);

        // Calculate similarity scores
        $similarities = [];
        foreach ($embeddings as $embedding) {
            dd($embedding);
            $storedEmbedding = json_decode($embedding->embedding, true);
            $similarity = $this->cosineSimilarity($promptEmbedding, $storedEmbedding);
            $similarities[$embedding->file_path] = $similarity;
        }

        // Sort by similarity
        arsort($similarities);

        // Get top results
        $topFilePaths = array_slice(array_keys($similarities), 0, 5);

        // Fetch and display the relevant records
        $similarSnippets = Embedding::whereIn('file_path', $topFilePaths)->get();
        $this->info($similarSnippets);
        foreach ($similarSnippets as $snippet) {
            $this->info('File Path: '.$snippet->file_path);
            $this->line("Code Snippet: \n".file_get_contents($snippet->file_path)."\n\n");
        }
    }

    protected function generatePromptEmbedding($prompt)
    {
        $response = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $prompt,
        ]);

        return $response['data'][0]['embedding'];
    }

    protected function fetchRelevantEmbeddings($contextTags)
    {
        return Embedding::all();
        //        return Embedding::whereHas('contexts', function ($query) use ($contextTags) {
        //            $query->whereIn('context', $contextTags);
        //        })->get();
    }

    protected function cosineSimilarity($vecA, $vecB)
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

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }
}
