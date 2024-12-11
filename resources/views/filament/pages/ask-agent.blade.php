<x-filament-panels::page>
    <div class="space-y-6">
        <div class="filament-messages-container dark:bg-gray-900 rounded-lg shadow-sm">
            @foreach($this->chats as $chat)
                <div wire:key="chat-{{ $chat->id }}" 
                     class="p-4 transition dark:bg-gray-800 dark:border-gray-700 border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium dark:text-gray-200">
                            {{ $chat->title }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            @if($chat->messages->last()?->metadata['model'])
                                <span class="px-2 py-1 text-xs rounded-full dark:bg-gray-700 dark:text-gray-300">
                                    {{ $chat->messages->last()?->metadata['model'] }}
                                </span>
                            @endif
                            <span class="text-sm dark:text-gray-400">
                                {{ $chat->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 space-y-4">
                        @foreach($chat->messages as $message)
                            <div class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-3/4 p-3 rounded-lg {{ 
                                    $message->role === 'user' 
                                        ? 'bg-amber-500 dark:bg-amber-600 text-white ml-4' 
                                        : 'bg-gray-700 dark:bg-gray-700 dark:text-gray-200 mr-4' 
                                }}">
                                    {{ $message->content }}
                                    @if($message->metadata['fallback'] ?? false)
                                        <div class="mt-1 text-xs text-gray-300">
                                            Fallback response using {{ $message->metadata['model'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="dark:bg-gray-800 rounded-lg shadow-sm p-4 space-y-4">
            <form wire:submit.prevent="sendMessage" class="space-y-4">
                {{ $this->form }}
                
                <div class="flex justify-end">
                    <x-filament::button
                        type="submit"
                        color="warning"
                        class="dark:bg-amber-400 dark:text-gray-900 dark:hover:bg-amber-500">
                        Send Message
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>