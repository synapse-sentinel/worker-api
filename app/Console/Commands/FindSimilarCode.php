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
            $this->info('Processing: '.$embedding->file_path);
            $storedEmbedding = json_decode($embedding->embedding, true);
            if (! is_array($storedEmbedding)) {
                $this->error('Invalid embedding for file: '.$embedding->file_path);

                continue;
            }
            $similarity = $this->cosineSimilarity($promptEmbedding, $storedEmbedding);
            $similarities[$embedding->file_path] = $similarity;
            $this->info('Similarity: '.$similarity);
        }

        // Sort by similarity
        arsort($similarities);

        // Get top results
        $topFilePaths = array_slice(array_keys($similarities), 0, 5);
        $this->info('Top File Paths: '.implode(', ', $topFilePaths));

        // Fetch and display the relevant records
        $similarSnippets = Embedding::whereIn('file_path', $topFilePaths)->get();
        foreach ($similarSnippets as $snippet) {
            $this->info('File Path: '.$snippet->file_path);
            try {
                $codeSnippet = file_get_contents($snippet->file_path);
                $this->line("Code Snippet: \n".$codeSnippet."\n\n");
            } catch (\Exception $e) {
                $this->error('Could not read file: '.$snippet->file_path);
            }
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
        // Uncomment and use this block if context tags are needed for filtering
        // return Embedding::whereHas('contexts', function ($query) use ($contextTags) {
        //     $query->whereIn('context', $contextTags);
        // })->get();

        // If context tags are not used, this can remain as is
        return Embedding::all();
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
