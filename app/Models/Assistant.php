<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create(mixed $validated)
 */
class Assistant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected  $fillable = ['name','instructions','ai_model_id'];

    /**
     * Get the aiModel that belongs to the Assistant
     */
    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }
}
