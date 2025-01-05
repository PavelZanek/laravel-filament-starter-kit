<?php

declare(strict_types=1);

use App\Filament\Pages\Tenancy\EditWorkspace;
use App\Models\User;
use App\Models\Workspace;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('returns correct label', function (): void {
    expect(EditWorkspace::getLabel())->toBe('Workspace Settings');
});

it('can edit a workspace', function (): void {
    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    $workspace = $user->workspaces->firstOrFail();
    $newData = Workspace::factory()->make();

    Filament::setTenant($workspace);

    livewire(EditWorkspace::class, [
        'tenant' => $workspace->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $workspace->name,
        ])
        ->fillForm([
            'name' => $newData->name,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($workspace->refresh())->toBeInstanceOf(Workspace::class)
        ->and($workspace->name)->toBe($newData->name)
        ->and($workspace->users)->toHaveCount(1)
        ->and($workspace->users->first()->id)->toBe($user->id);
});

it('can validate input', function (): void {
    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    $workspace = $user->workspaces->firstOrFail();

    Filament::setTenant($workspace);

    livewire(EditWorkspace::class, [
        'tenant' => $workspace->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $workspace->name,
        ])
        ->fillForm([
            'name' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});
