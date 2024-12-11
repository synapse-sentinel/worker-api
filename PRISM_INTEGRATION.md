# Prism Integration Analysis

## Overview
Prism is a powerful API that provides a unified interface for multiple LLM providers, making it an excellent choice for our multi-model architecture.

## Key Features to Leverage
1. Multi-Provider Support
   - OpenAI
   - Anthropic
   - Google VertexAI
   - AWS Bedrock
   - Azure OpenAI
   - Cohere
   - Mistral

2. Unified API Interface
   - Single interface for all models
   - Consistent response format
   - Standardized error handling

3. Advanced Features
   - Function calling
   - Vision capabilities
   - Streaming responses
   - Tool usage
   - System prompts

## Integration Plan

### 1. Initial Setup
```php
composer require echolabs/prism-client
```

### 2. Configuration
- Update .env file:
```env
PRISM_API_KEY=your_api_key
PRISM_BASE_URL=https://api.prism.echolabs.dev
```

### 3. Model Abstraction Layer

```php
namespace App\Services\AI;

interface ModelInterface
{
    public function chat(array $messages, array $options = []);
    public function complete(string $prompt, array $options = []);
    public function embeddings(array $input);
}

class PrismModelAdapter implements ModelInterface
{
    private $client;
    private $model;

    public function __construct(string $model)
    {
        $this->client = new \EchoLabs\PrismClient\Client([
            'api_key' => config('services.prism.api_key'),
            'base_url' => config('services.prism.base_url'),
        ]);
        $this->model = $model;
    }

    public function chat(array $messages, array $options = [])
    {
        return $this->client->chat->create([
            'model' => $this->model,
            'messages' => $messages,
            ...$options
        ]);
    }

    // Additional method implementations...
}
```

### 4. Database Updates Needed

Add to ai_models table:
- provider_specific_config
- max_tokens
- supports_functions
- supports_vision
- cost_input_tokens
- cost_output_tokens

### 5. Feature Implementation Plan

#### Phase 1: Basic Integration
- [ ] Set up Prism client
- [ ] Create basic model adapter
- [ ] Test with simple completions
- [ ] Implement error handling

#### Phase 2: Advanced Features
- [ ] Implement streaming responses
- [ ] Add function calling support
- [ ] Set up vision capabilities
- [ ] Create tool usage framework

#### Phase 3: Multi-Model Management
- [ ] Implement model switching
- [ ] Add fallback mechanisms
- [ ] Create model selection logic
- [ ] Set up cost tracking

### 6. Prism-Specific Features to Implement

1. Model Management
```php
public function getAvailableModels()
{
    return $this->client->models->list();
}
```

2. Streaming Support
```php
public function streamChat(array $messages)
{
    return $this->client->chat->create([
        'model' => $this->model,
        'messages' => $messages,
        'stream' => true
    ]);
}
```

3. Function Calling
```php
public function chatWithFunctions(array $messages, array $functions)
{
    return $this->client->chat->create([
        'model' => $this->model,
        'messages' => $messages,
        'functions' => $functions,
        'function_call' => 'auto'
    ]);
}
```

### 7. Filament Integration

Create Filament resources for:

1. Model Management
```php
namespace App\Filament\Resources;

use App\Filament\Resources\AIModelResource\Pages;
use App\Models\AIModel;
use Filament\Forms;
use Filament\Resources\Resource;

class AIModelResource extends Resource
{
    protected static ?string $model = AIModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Select::make('provider')
                    ->options([
                        'openai' => 'OpenAI',
                        'anthropic' => 'Anthropic',
                        'vertexai' => 'Google VertexAI',
                        'bedrock' => 'AWS Bedrock',
                        'azure' => 'Azure OpenAI',
                        'cohere' => 'Cohere',
                        'mistral' => 'Mistral',
                    ])
                    ->required(),
                // Additional fields...
            ]);
    }
}
```

### 8. Testing Strategy

1. Unit Tests
```php
namespace Tests\Unit;

use App\Services\AI\PrismModelAdapter;
use Tests\TestCase;

class PrismModelAdapterTest extends TestCase
{
    public function test_can_chat_with_model()
    {
        $adapter = new PrismModelAdapter('gpt-4');
        $response = $adapter->chat([
            ['role' => 'user', 'content' => 'Hello']
        ]);
        
        $this->assertNotNull($response);
        $this->assertArrayHasKey('choices', $response);
    }
}
```

### 9. Monitoring and Logging

Implement monitoring using Laravel Pulse:
- Token usage
- Response times
- Error rates
- Cost tracking
- Model performance metrics

## Migration Notes

1. Replace OpenAI calls:
   - Search for direct OpenAI usage
   - Replace with Prism adapter
   - Test each replacement
   - Monitor for errors

2. Update environment configuration:
   - Remove OpenAI specific configs
   - Add Prism configurations
   - Update documentation

3. Database updates:
   - Add new model capabilities
   - Migrate existing model data
   - Update indexes for performance

## Next Steps

1. Set up development environment with Prism
2. Create proof of concept with basic chat
3. Test streaming capabilities
4. Implement model switching logic
5. Create Filament interface for model management

