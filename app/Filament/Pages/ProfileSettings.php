<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ProfileSettings extends Page
{
    protected static bool $shouldRegisterNavigation = false;  

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.profile-settings';

    protected function getForms(): array
    {
        $forms = [
            'pages.auth.resume-file-upload-form-component',
        ];

        return $forms;
    }
}
