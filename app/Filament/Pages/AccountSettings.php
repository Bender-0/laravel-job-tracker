<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AccountSettings extends Page
{
    protected static bool $shouldRegisterNavigation = false;  

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.account-settings';

    protected function getForms(): array
    {
        $forms = [
            'pages.auth.name-and-email-form-component',
            'pages.auth.password-form-component',
            'pages.auth.browser-sessions-form-component',
            'pages.auth.delete-account-form-component',
        ];

        return $forms;
    }
}
