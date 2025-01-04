<?php

declare(strict_types=1);

use App\Filament\Pages\Tenancy\RegisterWorkspace;
use App\Models\User;
use App\Models\Workspace;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('returns correct label', function () {
    expect(RegisterWorkspace::getLabel())->toBe('Register workspace');
});

it('can create a workspace and attaches the user', function () {
    $user = User::factory()->create();
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

    expect($workspace = Workspace::query()->firstOrFail())->toBeInstanceOf(Workspace::class)
        ->and($workspace->name)->toBe($newData->name)
        ->and($workspace->users)->toHaveCount(1)
        ->and($workspace->users->first()->id)->toBe($user->id);
});

it('can validate input', function () {
    livewire(RegisterWorkspace::class)
        ->fillForm([
            'name' => null,
        ])
        ->call('register')
        ->assertHasFormErrors(['name' => 'required']);
});
