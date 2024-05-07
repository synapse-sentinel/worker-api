<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'description',
        'provider_value',
    ];

    /**
     * Get the messages for the thread.
     */
    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function summarize(): void
    {
        $this->update([
            'description' => json_encode([
                'messages' => $this->messages->map(function (Message $message) {
                    return [
                        'user' => $message->user->name,
                        'content' => $message->content,
                    ];
                }),
            ]),
        ]);
    }
}
