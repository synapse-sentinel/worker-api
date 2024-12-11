<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Chat;
use App\Services\AI\ChatService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class AskAgent extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Ask Agent';
    protected static ?string $title = 'Ask Agent';
    protected static ?string $slug = 'ask-agent';

    public ?string $message = null;
    public ?string $selectedAgent = 'general';
    public ?Chat $currentChat = null;
    
    protected static string $view = 'filament.pages.ask-agent';

    public function sendMessage(ChatService $chatService): void
    {
        try {
            if (!$this->currentChat) {
                $this->currentChat = Chat::create([
                    'user_id' => auth()->id(),
                    'title' => 'New Chat',
                ]);
            }

            // Set context based on agent type
            $context = match($this->selectedAgent) {
                'code' => ['type' => 'code'],
                'writing' => ['type' => 'writing'],
                default => ['type' => 'general'],
            };

            $response = $chatService->sendMessage(
                $this->currentChat,
                $this->message,
                $context
            );

            $this->message = null;
            $this->reset('message');

            Notification::make()
                ->title('Message sent')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error sending message')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('newThread')
                ->label('New Chat')
                ->icon('heroicon-o-plus')
                ->button()
                ->color('warning')
                ->action(fn () => $this->currentChat = null)
                ->outlined()
                ->extraAttributes([
                    'class' => 'dark:bg-gray-800 dark:text-amber-400 dark:border-amber-400 
                              dark:hover:bg-amber-400 dark:hover:text-gray-900',
                ]),
        ];
    }

    #[Computed]
    public function chats()
    {
        return Chat::latest()
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->paginate(10);
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedAgent')
                ->label('Select Agent')
                ->options([
                    'general' => 'General Assistant',
                    'code' => 'Code Assistant',
                    'writing' => 'Writing Assistant',
                ])
                ->default('general')
                ->required()
                ->extraAttributes([
                    'class' => 'dark:bg-gray-800 dark:border-gray-700 
                              dark:text-gray-200 dark:focus:border-amber-400',
                ]),

            Textarea::make('message')
                ->label('Message')
                ->rows(3)
                ->placeholder('Type your message here...')
                ->required()
                ->extraAttributes([
                    'class' => 'dark:bg-gray-800 dark:border-gray-700 
                              dark:text-gray-200 dark:focus:border-amber-400
                              dark:placeholder-gray-500',
                ]),
        ];
    }
}