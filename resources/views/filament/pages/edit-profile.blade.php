<x-filament::page>
    <form wire:submit="updateProfile">
        {{ $this->editProfileForm }}

        <div class="fi-form-actions">
            @foreach ($this->getUpdateProfileFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>

    <form wire:submit="updatePassword">
        {{ $this->editPasswordForm }}

        <div class="fi-form-actions">
            @foreach ($this->getUpdatePasswordFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
</x-filament::page>
