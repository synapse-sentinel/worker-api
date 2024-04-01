<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'name',
        'owned_by',
    ];
}
