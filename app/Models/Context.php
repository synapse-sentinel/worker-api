<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Context extends Model
{
    use HasFactory;

    protected $fillable = [
        'context',
    ];

    public function embeddings(): BelongsToMany
    {
        return $this->belongsToMany(Embedding::class);
    }
}
