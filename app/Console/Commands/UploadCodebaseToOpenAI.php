<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use OpenAI\Laravel\Facades\OpenAI;

class UploadCodebaseToOpenAI extends Command
{
    protected $signature = 'codebase:upload-to-openai';

    protected $description = 'Uploads the entire codebase to OpenAI vector store';

    public function handle()
    {
        // Define the path to the codebase
        $codebasePath = base_path();
        $this->info("Codebase path: $codebasePath");

        // Read all files in the codebase
        $files = $this->getCodebaseFiles($codebasePath);
        $this->info('Total files found: '.count($files));

        // Process and upload each file to the vector store
        foreach ($files as $file) {
            $this->info("Processing file: $file");
            $content = File::get($file);
            $this->uploadToOpenAI($file, $content);
        }

        $this->info('Codebase uploaded to OpenAI successfully.');
    }

    protected function getCodebaseFiles($path)
    {
        $allFiles = File::allFiles($path);
        $codebaseFiles = [];

        foreach ($allFiles as $file) {
            $filePath = $file->getPathname();
            // Filter out non-PHP files and exclude vendor and node_modules directories
            if ($file->getExtension() === 'php' &&
                strpos($filePath, 'vendor') === false &&
                strpos($filePath, 'node_modules') === false) {
                $codebaseFiles[] = $filePath;
            }
        }

        // Include package.json and composer.json
        if (File::exists($path.'/package.json')) {
            $codebaseFiles[] = $path.'/package.json';
        }
        if (File::exists($path.'/composer.json')) {
            $codebaseFiles[] = $path.'/composer.json';
        }

        return $codebaseFiles;
    }

    protected function uploadToOpenAI($filePath, $content)
    {
        $this->info("Uploading file: $filePath");

        // Example upload logic using OpenAI PHP for Laravel package
        $response = OpenAI::files()->upload([
            'purpose' => 'assistants',
            'file' => fopen($filePath, 'r'),
        ]);

        $this->info('File uploaded successfully. File ID: '.$response->id);

    }
}
