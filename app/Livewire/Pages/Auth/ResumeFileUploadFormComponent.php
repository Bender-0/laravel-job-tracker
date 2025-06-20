<?php

namespace App\Livewire\Pages\Auth;

use Filament\Forms\Form;
use Illuminate\Support\Str;
use App\Livewire\BaseFormComponent;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ResumeFileUploadFormComponent extends BaseFormComponent
{
    protected static string $view = 'livewire.pages.auth.resume-file-upload-form-component';
    public ?array $data = [];
    public $user;

    public function mount(): void  
    {  
        $this->user = $this->getUser();  
        
        // Fill form with existing resume data if it exists  
        $resumeData = [];  
        if ($this->user->resume) {  
            $resumeData['resume'] = $this->user->resume->resume;  
        }  
        
        $this->form->fill($resumeData); 
        // dd($this->user->resume()->get());
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
                        ->disk('public')
                        ->acceptedFileTypes(['application/pdf', 'application/msword'])
                        ->visibility('public')
                        ->maxSize(10240)
                        ->downloadable()
                        ->openable()
                        ->saveUploadedFileUsing(fn ($component, $file) => 
                            self::saveUploadedFile($component, $file, 'resume')
                        )
                        ->deletable()
                        ->deleteUploadedFileUsing(function (string $file, $component) {
                            // Delete the file from storage
                            Storage::disk('public')->delete($file);
                            
                            // Get the user's resume and delete it
                            $user = $component->getRecord();
                            if ($user && $user->resume) {
                                $user->resume()->delete();
                            }
                        })
                    ])
            ])
            ->model($this->user)
            ->statePath('data');
    }

    private function saveUploadedFile(
        FileUpload $component, 
        TemporaryUploadedFile $file, 
        string $column
    ): string {
        $userName = $this->user->name;
        $sanitizedUserName = $this->sanitizeInput($userName);
        
        $originalExtension = $file->getClientOriginalExtension();
        $typeLabel = 'GENERAL_RESUME';
        $filename = "{$sanitizedUserName}_{$typeLabel}.{$originalExtension}";
        
        return $file->storeAs($component->getDirectory(), $filename, $component->getDiskName());
    }

    private function sanitizeInput(string $input): string
    {
        return Str::of($input)
            ->replace([',', '.', '&', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '-', "'"], '')
            ->snake()
            ->upper();
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
