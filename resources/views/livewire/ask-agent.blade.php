
<div class="chat-container">
    <div class="chat-header">
        <h2>Chat with AI Agent</h2>
    </div>
    
    <div class="chat-messages">
        @foreach ($messages as $message)
            <div class="chat-message">
                @markdown($message->content)
            </div>
        @endforeach
    </div>
    
    <div class="chat-input">
        <textarea wire:model="newMessage" placeholder="Type your message..."></textarea>
        <button wire:click="sendMessage">Send</button>
    </div>
</div>
