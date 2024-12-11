
<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use League\CommonMark\CommonMarkConverter;
use Livewire\Component;

class AskAgent extends Component
{
    public $chat;
    public $messages = [];
    public $newMessage = '';

    public function mount($chatId)
    {
        $this->chat = Chat::findOrFail($chatId);
        $this->messages = $this->chat->messages;
    }

    public function render()
    {
        $converter = new CommonMarkConverter();

        return view('livewire.ask-agent', [
            'messages' => $this->messages->map(function ($message) use ($converter) {
                $message->content = $converter->convert($message->content)->getContent();
                return $message;
            }),
        ]);
    }

    public function sendMessage()
    {
        // Add the new message to the messages array
        $this->messages[] = [
            'text' => $this->newMessage,
            'sender' => 'user',
        ];

        $this->newMessage = '';

        // TODO: Implement sending message to the AI agent and receiving response
    }
}
