<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        // 데모 계정 정보 자동 입력
        $this->form->fill([
            'email' => 'admin@example.com',
            'password' => 'Demo@WB2025!',
            'remember' => true,
        ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('이메일')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('비밀번호')
            ->password()
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }
}
