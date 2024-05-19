<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAI\Laravel\Facades\OpenAI;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @method static create(mixed $validated)
 *
 * @property mixed $name
 */
class Assistant extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'instructions',
        'ai_model_id',
        'provider_value',
        'user_id',
    ];

    /**
     * Get the aiModel that belongs to the Assistant
     */
    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Message::class,
            User::class,
            'id', // Foreign key on the users table...
            'user_id', // Foreign key on the messages table...
            'user_id', // Local key on the assistants table...
            'id' // Local key on the users table...
        );
    }

    public function messageRecommendations(): HasMany
    {
        return $this->hasMany(MessageRecommendation::class);
    }

    public function processMessage($message): \OpenAI\Responses\Threads\Runs\ThreadRunResponse
    {
        $thread = $message->thread;
        $messages = $thread->messages->select('role', 'content')->toArray();
        $threadRun = OpenAI::threads()->createAndRun(
            [
                'assistant_id' => $this->provider_value,
                'thread' => [

                    'messages' => $messages,
                ],
            ]);

        $message->thread->update(['provider_value' => $threadRun->threadId]);

        return $threadRun;
    }

    public function reflect(): void
    {
        $prompt = view('prompts.agent-reflect', [
            'assistant' => $this,
            'threads' => Thread::inRandomOrder()
                ->limit(2)
                ->pluck('description'),
        ])->render();

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'assistant', 'content' => $prompt],
            ],
        ]);

        $instructions = $response['choices'][0]['message']['content'];

        $newDescription = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'assistant', 'content' => 'please provide yourself updated instructions from this reflection:'
                    .$instructions.
                    'referring your previous instructions.'.$this->instructions.' please provide updated instructions.',
                ],
            ],
        ]);
        $this->instructions = $newDescription['choices'][0]['message']['content'];
        $this->save();
    }

    public function processThread($thread): void
    {
        if (! $thread) {
            return;
        }
        $messages = $thread->messages->select('role', 'content')->toArray();
        $threadRun = OpenAI::threads()->createAndRun(
            [
                'assistant_id' => $this->provider_value,
                'thread' => [
                    'messages' => $messages,
                ],
            ]);

        $thread->update(['provider_value' => $threadRun->threadId]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'instructions']);
    }
}
