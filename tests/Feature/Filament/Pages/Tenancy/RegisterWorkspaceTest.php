<?php

declare(strict_types=1);

use App\Filament\Pages\Tenancy\RegisterWorkspace;
use App\Models\User;
use App\Models\Workspace;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('returns correct label', function (): void {
    expect(RegisterWorkspace::getLabel())->toBe(__('common.workspaces.labels.register'));
});

it('can create a workspace and attaches the user', function (): void {
    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    $newData = Workspace::factory()->make();

    livewire(RegisterWorkspace::class)
        ->fillForm([
            'name' => $newData->name,
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Workspace::class, [
        'name' => $newData->name,
    ]);

    expect($workspace = Workspace::query()->where('name', $newData->name)->first())
        ->toBeInstanceOf(Workspace::class)
        ->and($workspace->name)->toBe($newData->name)
        ->and($workspace->users)->toHaveCount(1)
        ->and($workspace->users->first()->id)->toBe($user->id);
});

it('can validate input', function (): void {
    livewire(RegisterWorkspace::class)
        ->fillForm([
            'name' => null,
        ])
        ->call('register')
        ->assertHasFormErrors(['name' => 'required']);
});
