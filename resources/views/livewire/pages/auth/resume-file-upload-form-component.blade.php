<x-filament-panels::form wire:submit="updateResume" class="py-5">
    {{ $this->form }}

    <div class="fi-form-actions">
        <div class="flex flex-row-reverse flex-wrap items-center gap-3 fi-ac">
            <x-filament::button type="submit">  
                {{ __('Upload Resume') }}  
            </x-filament::button>  
        </div>
    </div>
</x-filament-panels::form>