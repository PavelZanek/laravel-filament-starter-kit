<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Pages;

use App\Filament\Admin\Pages\EditProfile as AdminEditProfile;
use App\Filament\Pages\EditProfile as AppEditProfile;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can update profile information', function (string $editProfileClass, string $panelId): void {
    $user = User::factory()->withWorkspaces()->withRole()->create();
    actingAs($user);

    if ($panelId === 'app') {
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($user->getActiveTenant());
    } else {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    $newData = User::factory()->make();

    livewire($editProfileClass)
        ->assertFormSet([
            'name' => $user->name,
            'email' => $user->email,
        ], 'editProfileForm')
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
        ], 'editProfileForm')
        ->call('updateProfile')
        ->assertHasNoFormErrors([], 'editProfileForm');

    expect($user->fresh())
        ->name->toBe($newData->name)
        ->email->toBe($newData->email);
})->with([
    'app panel' => [AppEditProfile::class, 'app'],
    'admin panel' => [AdminEditProfile::class, 'admin'],
]);

it('validates profile input', function (string $editProfileClass, string $panelId): void {
    $user = User::factory()->withWorkspaces()->withRole()->create();
    actingAs($user);

    if ($panelId === 'app') {
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($user->getActiveTenant());
    } else {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    livewire($editProfileClass)
        ->fillForm([
            'name' => '',
            'email' => 'not-an-email',
        ], 'editProfileForm')
        ->call('updateProfile')
        ->assertHasFormErrors(['name' => 'required', 'email' => 'email'], 'editProfileForm');
})->with([
    'app panel' => [AppEditProfile::class, 'app'],
    'admin panel' => [AdminEditProfile::class, 'admin'],
]);

it('can update password', function (string $editProfileClass, string $panelId): void {
    $user = User::factory()
        ->withWorkspaces()
        ->withRole()
        ->create(['password' => Hash::make('old-password')]);

    actingAs($user);

    if ($panelId === 'app') {
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($user->getActiveTenant());
    } else {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    $newPassword = 'new-secure-password';

    livewire($editProfileClass)
        ->fillForm([
            'currentPassword' => 'old-password',
            'password' => $newPassword,
            'passwordConfirmation' => $newPassword,
        ], 'editPasswordForm')
        ->call('updatePassword')
        ->assertHasNoFormErrors([], 'editPasswordForm');

    expect(Hash::check($newPassword, $user->fresh()->password))->toBeTrue();
})->with([
    'app panel' => [AppEditProfile::class, 'app'],
    'admin panel' => [AdminEditProfile::class, 'admin'],
]);

it('validates password input', function (string $editProfileClass, string $panelId): void {
    $user = User::factory()
        ->withWorkspaces()
        ->withRole()
        ->create(['password' => Hash::make('old-password')]);

    actingAs($user);

    if ($panelId === 'app') {
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($user->getActiveTenant());
    } else {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    livewire($editProfileClass)
        ->fillForm([
            'currentPassword' => 'wrong-password',
            'password' => 'short',
            'passwordConfirmation' => 'different',
        ], 'editPasswordForm')
        ->call('updatePassword')
        ->assertHasFormErrors([
            'currentPassword' => 'current_password',
            // 'password' => ['min', 'password'], // TODO
            // 'passwordConfirmation' => 'same', // TODO
        ], 'editPasswordForm');
})->with([
    'app panel' => [AppEditProfile::class, 'app'],
    'admin panel' => [AdminEditProfile::class, 'admin'],
]);
