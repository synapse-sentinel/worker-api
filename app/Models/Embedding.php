<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Embedding extends Model
{
    use HasFactory;

    protected $fillable = [
        'context',
        'file_path',
        'embedding',
    ];

    public function contexts(): BelongsToMany
    {
        return $this->belongsToMany(Context::class);
    }
}
