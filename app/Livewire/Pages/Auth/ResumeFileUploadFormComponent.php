<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use Filament\Forms\Form;
use App\Livewire\BaseFormComponent;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;

class ResumeFileUploadFormComponent extends BaseFormComponent
{
    protected static string $view = 'livewire.pages.auth.resume-file-upload-form-component';
    public ?array $data = [];

    public function mount(): void  
    {  
        $this->user = $this->getUser();  
        
        // Fill form with existing resume data if it exists  
        $resumeData = [];  
        if ($this->user->resume) {  
            $resumeData['resume'] = $this->user->resume->resume;  
        }  
        
        $this->form->fill($resumeData);  
        // Remove the dd() for production  
    }  


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('resume'))
                    ->aside()
                    ->description(__('Upload your resume.'))
                    ->schema([
                        FileUpload::make('resume')
                            ->label(__('Resume'))
                            ->required(),
                    ])
            ])
            ->model($this->user)
            ->statePath('data');
    }

    public function updateResume(): void      
    {      
        try {      
            $data = $this->form->getState();      
    
            // Create or update the resume relationship    
            $this->user->resume()->updateOrCreate(    
                ['user_id' => $this->user->id],    
                ['resume' => $data['resume']]    
            );    
        } catch (Halt $exception) {      
            return;      
        }      
    
        // Refill form with updated data to show the uploaded file  
        $this->form->fill(['resume' => $data['resume']]);      
    
        Notification::make()      
            ->success()      
            ->title(__('Your resume has been uploaded successfully.'))      
            ->send();      
    }
}
