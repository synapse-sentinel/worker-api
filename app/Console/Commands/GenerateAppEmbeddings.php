<?php

namespace App\Console\Commands;

use App\Models\Context;
use App\Models\Embedding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateAppEmbeddings extends Command
{
    protected $signature = 'embeddings:generate-app';

    protected $description = 'Generate and store embeddings for the entire application';

    public function handle()
    {
        $directory = base_path();
        $this->info("Generating embeddings for directory: $directory");

        // Exclude unnecessary directories
        $excludeDirs = ['vendor', 'node_modules', 'bootstrap', 'storage', 'public', 'resources'];

        // Exclude unnecessary file extensions
        $excludeExtensions = ['.js', '.css', '.map', '.lock', '.xml', '.md', 'sqlite', 'png', 'stub'];

        // Read all files in the specified directory
        $files = $this->getAllFiles($directory, $excludeDirs, $excludeExtensions);

        foreach ($files as $file) {
            $filePath = $file->getPathname();
            $this->info("Processing file: $filePath");

            $content = File::get($filePath);
            $contentHash = hash('sha256', $content);

            // Check if embedding already exists and if the content has changed
            $existingEmbedding = Embedding::where('file_path', $filePath)->first();
            if ($existingEmbedding && $existingEmbedding->content_hash === $contentHash) {
                $this->info("No changes detected for file: $filePath. Skipping.");

                continue;
            }

            $embedding = $this->generateEmbedding($content);
            $contextTags = $this->determineContext($content);

            $this->storeEmbedding($filePath, $embedding, $contentHash, $contextTags);
        }

        $this->info('Embeddings generation completed successfully.');
    }

    protected function getAllFiles($directory, $excludeDirs = [], $excludeExtensions = [])
    {
        $allFiles = File::allFiles($directory);

        return collect($allFiles)->filter(function ($file) use ($excludeDirs, $excludeExtensions) {
            $filePath = $file->getPathname();

            // Exclude files based on directory
            foreach ($excludeDirs as $excludeDir) {
                if (strpos($filePath, DIRECTORY_SEPARATOR.$excludeDir.DIRECTORY_SEPARATOR) !== false) {
                    return false;
                }
            }

            // Exclude files based on extension
            foreach ($excludeExtensions as $excludeExtension) {
                if (str_ends_with($filePath, $excludeExtension)) {
                    return false;
                }
            }

            return true;
        });
    }

    protected function generateEmbedding($content)
    {
        $response = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $content,
        ]);

        return $response['data'][0]['embedding'];
    }

    protected function determineContext($content)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => "Determine the context of the following code with a few comma separated categories:\n\n$content\n\nThe context is:"],
            ],
            'max_tokens' => 50,
        ]);

        return array_map('trim', explode(',', trim($response['choices'][0]['message']['content'])));
    }

    protected function storeEmbedding($filePath, $embedding, $contentHash, $contextTags)
    {
        $embeddingModel = Embedding::updateOrCreate(
            ['file_path' => $filePath],
            [
                'embedding' => json_encode($embedding),
                'content_hash' => $contentHash,
            ]
        );

        // Sync contexts using the pivot table
        $contextIds = [];
        foreach ($contextTags as $tag) {
            $context = Context::firstOrCreate(['context' => $tag]);
            $contextIds[] = $context->id;
        }

        $embeddingModel->contexts()->sync($contextIds);
    }
}
