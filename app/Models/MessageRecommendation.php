<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'assistant_id',
        'points',
        'reason',

    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(Assistant::class);
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }
}
