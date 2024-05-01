<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * @method static create(mixed $validated)
 *
 * @property mixed $name
 */
class Assistant extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'avatar', 'instructions', 'ai_model_id', 'provider_value'];

    /**
     * Get the aiModel that belongs to the Assistant
     */
    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }

    public function messageRecommendations(): HasMany
    {
        return $this->hasMany(MessageRecommendation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
