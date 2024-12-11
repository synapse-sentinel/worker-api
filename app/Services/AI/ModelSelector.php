<?php

namespace App\Services\AI;

use Illuminate\Support\Str;

class ModelSelector
{
    public const MODEL_PREFERENCES = [
        'code' => [
            'primary' => 'claude-3-opus-20240229',
            'fallback' => 'gpt-4-turbo-preview',
        ],
        'analysis' => [
            'primary' => 'claude-3-opus-20240229',
            'fallback' => 'gpt-4-turbo-preview',
        ],
        'chat' => [
            'primary' => 'claude-3-sonnet-20240229',
            'fallback' => 'gpt-3.5-turbo',
        ],
        'function' => [
            'primary' => 'gpt-4-turbo-preview',
            'fallback' => 'claude-3-sonnet-20240229',
        ],
        'vision' => [
            'primary' => 'gpt-4-vision-preview',
            'fallback' => null,
        ],
    ];

    public function selectModel(string $content, array $context = []): string
    {
        // Detect code-related queries
        if ($this->isCodeRelated($content)) {
            return self::MODEL_PREFERENCES['code']['primary'];
        }

        // Detect analysis queries
        if ($this->isAnalysisQuery($content)) {
            return self::MODEL_PREFERENCES['analysis']['primary'];
        }

        // Check for function calling needs
        if ($this->needsFunctionCalling($context)) {
            return self::MODEL_PREFERENCES['function']['primary'];
        }

        // Check for vision requirements
        if ($this->hasImageContent($context)) {
            return self::MODEL_PREFERENCES['vision']['primary'];
        }

        // Default to general chat
        return self::MODEL_PREFERENCES['chat']['primary'];
    }

    protected function isCodeRelated(string $content): bool
    {
        $codeIndicators = [
            'code',
            'function',
            'class',
            'programming',
            'debug',
            'error',
            'syntax',
            'compile',
            'git',
            'npm',
            'composer',
        ];

        return Str::contains(strtolower($content), $codeIndicators);
    }

    protected function isAnalysisQuery(string $content): bool
    {
        $analysisIndicators = [
            'analyze',
            'review',
            'evaluate',
            'assess',
            'compare',
            'explain',
            'describe in detail',
            'what are the implications',
        ];

        return Str::contains(strtolower($content), $analysisIndicators);
    }

    protected function needsFunctionCalling(array $context): bool
    {
        return isset($context['require_functions']) && $context['require_functions'] === true;
    }

    protected function hasImageContent(array $context): bool
    {
        return isset($context['has_image']) && $context['has_image'] === true;
    }

    public function getFallbackModel(string $primaryModel): ?string
    {
        foreach (self::MODEL_PREFERENCES as $category) {
            if ($category['primary'] === $primaryModel) {
                return $category['fallback'];
            }
        }

        return self::MODEL_PREFERENCES['chat']['fallback'];
    }
}