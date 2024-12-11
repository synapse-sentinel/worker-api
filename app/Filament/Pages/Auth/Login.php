<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\URL;
use Spatie\LoginLink\LoginLink;

class Login extends BaseLogin
{
    protected function hasLoginLink(): bool
    {
        return app()->environment('local');
    }

    protected function getFormSchema(): array
    {
        $schema = parent::getFormSchema();

        if ($this->hasLoginLink()) {
            $schema[] = \Filament\Forms\Components\View::make('filament.pages.auth.login-link');
        }

        return $schema;
    }

    public function loginLink()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        
        if (!$user) {
            return;
        }

        $link = LoginLink::createForUser($user);

        return redirect($link->url);
    }
}