<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Pages;

use App\Filament\Pages\EditProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can update profile information', function (): void {
    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    $newData = User::factory()->make();

    livewire(EditProfile::class)
        ->assertFormSet([
            'name' => $user->name,
            'email' => $user->email,
        ], 'editProfileForm')
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
        ], 'editProfileForm')
        ->call('updateProfile')
        ->assertHasNoFormErrors(formName: 'editProfileForm');

    expect($user->fresh())
        ->name->toBe($newData->name)
        ->email->toBe($newData->email);
});

it('validates profile input', function (): void {
    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    livewire(EditProfile::class)
        ->fillForm([
            'name' => '',
            'email' => 'not-an-email',
        ], 'editProfileForm')
        ->call('updateProfile')
        ->assertHasFormErrors(['name' => 'required', 'email' => 'email'], 'editProfileForm');
});

it('can update password', function (): void {
    $user = User::factory()->withWorkspaces()->create(['password' => Hash::make('old-password')]);
    actingAs($user);

    $newPassword = 'new-secure-password';

    livewire(EditProfile::class)
        ->fillForm([
            'Current password' => 'old-password',
            'password' => $newPassword,
            'passwordConfirmation' => $newPassword,
        ], 'editPasswordForm')
        ->call('updatePassword')
        ->assertHasNoFormErrors(formName: 'editPasswordForm');

    expect(Hash::check($newPassword, $user->fresh()->password))->toBeTrue();
});

it('validates password input', function (): void {
    $user = User::factory()->withWorkspaces()->create(['password' => Hash::make('old-password')]);
    actingAs($user);

    livewire(EditProfile::class)
        ->fillForm([
            'Current password' => 'wrong-password',
            'password' => 'short',
            'passwordConfirmation' => 'different',
        ], 'editPasswordForm')
        ->call('updatePassword')
        ->assertHasFormErrors([
            'Current password' => 'current_password',
            // 'password' => ['min', 'password'], // TODO
            // 'passwordConfirmation' => 'same', // TODO
        ], 'editPasswordForm');
});
