<div>
    @foreach ($this->getForms() as $component)
        @livewire($component)
    @endforeach
</div>