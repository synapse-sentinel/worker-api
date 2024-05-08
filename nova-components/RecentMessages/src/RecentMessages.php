<?php

namespace PartridgeRocks\RecentMessages;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Card;

class RecentMessages extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = 'full';

    /**
     * Get the component name for the element.
     */
    public function component(): string
    {
        return 'recent-messages';
    }

    // in nova-components/RecentMessages/src/RecentMessages.php

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'messages' => Message::whereNot('user_id', Auth::user()->getAuthIdentifier())->with('user') // assuming there's a user relationship
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'username' => $message->user->name, // adjust based on your user model
                        'created_at' => $message->created_at->toFormattedDateString(), // or any other format you prefer
                        'content' => $message->content,
                        'thread' => $message->thread, // adjust based on your message model
                    ];
                }),
        ]);
    }
}
