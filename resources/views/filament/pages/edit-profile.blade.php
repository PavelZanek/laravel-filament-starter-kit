@php
    use Filament\Support\Enums\Alignment;
@endphp

<x-filament-panels::page>
    <x-filament-panels::form wire:submit="updateProfile">
        {{ $this->editProfileForm }}

        <x-filament-panels::form.actions :actions="$this->getUpdateProfileFormActions()" :alignment="Alignment::End" />
    </x-filament-panels::form>

    <x-filament-panels::form wire:submit="updatePassword">
        {{ $this->editPasswordForm }}

        <x-filament-panels::form.actions :actions="$this->getUpdatePasswordFormActions()" :alignment="Alignment::End" />
    </x-filament-panels::form>
</x-filament-panels::page>
