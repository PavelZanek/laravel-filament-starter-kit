<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Pages;

use App\Filament\Admin\Resources\RoleResource;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    $this->user = User::factory()->withRole(Role::SUPER_ADMIN)->create();
    actingAs($this->user);
});

it('returns correct navigation labels and groups', function (): void {
    expect(RoleResource::getNavigationLabel())->toBeString()
        ->and(RoleResource::getNavigationGroup())->toBeString()
        ->and(RoleResource::getBreadcrumb())->toBeString();
});

it('provides relations and pages arrays', function (): void {
    expect(RoleResource::getRelations())->toBeArray()
        ->and(RoleResource::getPages())->toBeArray();
});

it('returns correct permission prefixes', function (): void {
    $prefixes = RoleResource::getPermissionPrefixes();

    expect($prefixes)->toBeArray()
        ->toContain('view')
        ->toContain('view_any')
        ->toContain('create')
        ->toContain('update')
        ->toContain('delete')
        ->toContain('delete_any');
});

it('can render the resource', function (): void {
    $this->get(RoleResource::getUrl())->assertSuccessful();
});

it('can not render the resource for disallowed user', function (): void {
    $user = User::factory()->withRole()->create();
    actingAs($user);

    $this->get(RoleResource::getUrl())->assertForbidden();
});

it('can list records', function (): void {
    $records = Role::factory()->count(1)->create(['name' => fake()->domainName, 'is_default' => false]);

    $records->add($this->user->roles()->first());

    livewire(RoleResource\Pages\ListRoles::class)
        ->assertCanSeeTableRecords($records);
});

it('returns correct title', function (): void {
    $listUsers = new RoleResource\Pages\ListRoles;

    expect($listUsers->getTitle())->toBe(__('admin/role-resource.list.title'));
});

it('dispatches scroll-to-top on page set', function (): void {
    livewire(RoleResource\Pages\ListRoles::class)
        ->call('setPage', 2)
        ->assertDispatched('scroll-to-top');
});

it('has column', function (string $column): void {
    livewire(RoleResource\Pages\ListRoles::class)
        ->assertTableColumnExists($column);
})->with(['name', 'guard_name', 'permissions_count', 'users_count', 'is_default', 'updated_at']);

it('can render column', function (string $column): void {
    livewire(RoleResource\Pages\ListRoles::class)
        ->assertCanRenderTableColumn($column);
})->with(['name', 'guard_name', 'permissions_count', 'users_count', 'is_default', 'updated_at']);

it('can search column', function (string $column): void {
    $records = Role::factory()->count(1)->create([
        'name' => 'Test',
        'is_default' => false,
    ]);

    $records->add($this->user->roles()->first());

    $value = $records->first()->{$column};

    livewire(RoleResource\Pages\ListRoles::class)
        ->searchTable($value)
        ->assertCanSeeTableRecords($records->where($column, $value))
        ->assertCanNotSeeTableRecords($records->where($column, '!=', $value));
})->with(['name']);

it('can not delete records from table', function (): void {
    $records = Role::query()->get();

    livewire(RoleResource\Pages\ListRoles::class)
        ->assertCanSeeTableRecords($records)
        ->assertActionDoesNotExist(DeleteAction::class)
        ->assertTableBulkActionDoesNotExist(DeleteBulkAction::class);
});

it('can render the create page', function (): void {
    $this->get(RoleResource::getUrl('create'))->assertSuccessful();
});

it('can not render the create page for disallowed user', function (): void {
    $user = User::factory()->withRole()->create();
    actingAs($user);

    $this->get(RoleResource::getUrl('create'))->assertForbidden();
});

it('can create a record', function (): void {
    livewire(RoleResource\Pages\CreateRole::class)
        ->fillForm([
            'name' => 'Test',
            'guard_name' => null,
        ])
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(RoleResource::getUrl('view', [
            'record' => Role::where('name', 'Test')->first()->getRouteKey(),
        ]));

    $this->assertDatabaseHas(Role::class, [
        'name' => 'Test',
        'guard_name' => Role::GUARD_NAME_WEB,
    ]);
});

it('can validate required', function (string $column): void {
    livewire(RoleResource\Pages\CreateRole::class)
        ->fillForm([$column => null])
        ->call('create')
        ->assertHasFormErrors([$column => ['required']]);
})->with(['name']);

it('can validate unique', function (string $column): void {
    $record = Role::factory()->create(['name' => 'Test']);

    livewire(RoleResource\Pages\CreateRole::class)
        ->fillForm(['name' => $record->name])
        ->call('create')
        ->assertHasFormErrors([$column => ['unique']]);
})->with(['name']);

it('can validate max length', function (string $column): void {
    livewire(RoleResource\Pages\CreateRole::class)
        ->fillForm([$column => Str::random(256)])
        ->call('create')
        ->assertHasFormErrors([$column => ['max']]);
})->with(['name', 'guard_name']);

it('can render the edit page', function (): void {
    $role = Role::factory()->create(['name' => 'Test', 'is_default' => false]);

    $this->get(RoleResource::getUrl('edit', [
        'record' => $role->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve record data', function (): void {
    $role = Role::factory()->create(['name' => 'Test', 'is_default' => false]);

    livewire(RoleResource\Pages\EditRole::class, [
        'record' => $role->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $role->name,
            'guard_name' => $role->guard_name,
        ]);
});

it('can update a record', function (): void {
    $record = Role::factory()->create(['name' => 'Test', 'is_default' => false]);

    livewire(RoleResource\Pages\EditRole::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newName = fake()->domainName,
            'guard_name' => Role::GUARD_NAME_WEB,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->name->toBe($newName)
        ->guard_name->toBe(Role::GUARD_NAME_WEB);
});

it('can validate required (edit page)', function (string $column): void {
    $record = Role::factory()->create(['name' => 'Test', 'is_default' => false]);

    livewire(RoleResource\Pages\EditRole::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([$column => null])
        ->call('save')
        ->assertHasFormErrors([$column => ['required']]);
})->with(['name']);

it('can validate unique (edit page)', function (string $column): void {
    $record = Role::factory()->create(['name' => 'Test', 'is_default' => false]);

    livewire(RoleResource\Pages\EditRole::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm(['name' => $this->user->roles()->first()->name])
        ->call('save')
        ->assertHasFormErrors([$column => ['unique']]);
})->with(['name']);

it('can validate max length (edit page)', function (string $column): void {
    $record = Role::factory()->create(['name' => 'Test', 'is_default' => false]);

    livewire(RoleResource\Pages\EditRole::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([$column => Str::random(256)])
        ->call('save')
        ->assertHasFormErrors([$column => ['max']]);
})->with(['name', 'guard_name']);

it('can delete a record', function (): void {
    $record = Role::factory()->create(['name' => 'Test', 'is_default' => false]);

    livewire(RoleResource\Pages\EditRole::class, [
        'record' => $record->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertDatabaseMissing(Role::class, [
        'id' => $record->id,
    ]);
})->skip();

it('can not delete default role', function (): void {
    livewire(RoleResource\Pages\EditRole::class, [
        'record' => $this->user->roles()->first()->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);
})->skip();

it('shows edit action for permitted non-default role', function (): void {
    $role = Role::factory()->create(['is_default' => false]);

    livewire(RoleResource\Pages\ViewRole::class, [
        'record' => $role->getRouteKey(),
    ])->assertActionExists(EditAction::class);
});

it('hides edit action for default role', function (): void {
    $role = Role::factory()->create();

    livewire(RoleResource\Pages\ViewRole::class, [
        'record' => $role->getRouteKey(),
    ])->assertActionHidden(EditAction::class);
});
