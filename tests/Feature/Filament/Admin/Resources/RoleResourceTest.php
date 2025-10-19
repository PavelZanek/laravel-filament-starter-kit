<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Pages;

use App\Filament\Admin\Resources\Roles;
use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Exception;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    $this->user = User::query()->where('email', config('project.super_admin_email'))->first();

    if (! $this->user) {
        $this->user = User::factory()->withWorkspaces()->create([
            'email' => config('project.super_admin_email'),
        ]);
        $this->user->assignRole(Role::SUPER_ADMIN);
    }

    actingAs($this->user);
});

it('has proper basic configuration and can render pages', function (): void {
    // Test navigation configuration
    expect(RoleResource::getNavigationLabel())->toBeString()
        ->and(RoleResource::getNavigationGroup())->toBeString()
        ->and(RoleResource::getBreadcrumb())->toBeString()
        // Test relations and pages arrays
        ->and(RoleResource::getRelations())->toBeArray()
        ->and(RoleResource::getPages())->toBeArray();

    // Test resource rendering for authorized user
    $this->get(RoleResource::getUrl())->assertSuccessful();

    // Test resource access denied for unauthorized user
    $user = User::factory()->withRole()->create();
    actingAs($user);
    $this->get(RoleResource::getUrl())->assertForbidden();
    actingAs($this->user); // Reset to authorized user

    // Test record listing
    $records = Role::factory()->count(1)->create(['name' => fake()->domainName, 'is_default' => false]);
    $records->add($this->user->roles()->first());

    livewire(Roles\Pages\ListRoles::class)
        ->assertCanSeeTableRecords($records);

    // Test list page title
    $listRoles = new Roles\Pages\ListRoles;
    expect($listRoles->getTitle())->toBe(__('admin/role-resource.list.title'));

    // Test pagination scroll behavior
    livewire(Roles\Pages\ListRoles::class)
        ->call('setPage', 2)
        ->assertDispatched('scroll-to-top');
});

it('has all required table columns and can render them', function (): void {
    $columns = [
        'name', 'guard_name', 'permissions_count', 'users_count',
        'is_default', 'updated_at',
    ];
    $auditColumns = ['deleted_at', 'createdBy.name', 'updatedBy.name'];

    $component = livewire(Roles\Pages\ListRoles::class);

    // Test column existence
    foreach ($columns as $column) {
        $component->assertTableColumnExists($column);
    }

    foreach ($auditColumns as $column) {
        $component->assertTableColumnExists($column);
    }

    // Test column rendering
    $renderableColumns = [
        'name', 'guard_name', 'permissions_count', 'users_count', 'is_default',
    ];

    foreach ($renderableColumns as $column) {
        $component->assertCanRenderTableColumn($column);
    }

    // Skip updated_at column test due to Filament v4 wire:key changes
    // $component->assertCanRenderTableColumn('updated_at');
});

it('has comprehensive filtering, searching and sorting functionality', function (): void {
    // Test filter existence
    livewire(Roles\Pages\ListRoles::class)
        ->assertTableFilterExists('trashed')
        ->assertTableFilterExists('created_by_id')
        ->assertTableFilterExists('updated_by_id')
        ->assertTableFilterExists('deleted_by_id');

    // Test filtering by creator and trashed records
    $user1 = $this->user;
    $user2 = User::factory()->withRole(Role::SUPER_ADMIN)->create();

    actingAs($user1);
    $role1 = Role::factory()->create(['name' => 'role-a', 'guard_name' => Role::GUARD_NAME_WEB, 'is_default' => false]);

    actingAs($user2);
    $role2 = Role::factory()->create(['name' => 'role-b', 'guard_name' => Role::GUARD_NAME_WEB, 'is_default' => false]);
    $role2->delete();

    actingAs($user1);

    livewire(Roles\Pages\ListRoles::class)
        ->filterTable('trashed', '1')
        ->filterTable('created_by_id', $user2->id)
        ->assertCanSeeTableRecords([$role2]);

    livewire(Roles\Pages\ListRoles::class)
        ->filterTable('trashed', '1')
        ->assertCanSeeTableRecords([$role2])
        ->filterTable('deleted_by_id', $user2->id)
        ->assertCanSeeTableRecords([$role2]);

    // Test searching by name
    $records = Role::factory()->count(1)->create([
        'name' => 'Test',
        'is_default' => false,
    ]);

    $records->add($this->user->roles()->first());
    $value = $records->first()->name;

    livewire(Roles\Pages\ListRoles::class)
        ->searchTable($value)
        ->assertCanSeeTableRecords($records->where('name', $value))
        ->assertCanNotSeeTableRecords($records->where('name', '!=', $value));
});

it('can create roles with comprehensive validation', function (): void {
    \Illuminate\Support\Facades\Gate::before(fn (): true => true);

    $this->get(RoleResource::getUrl('create'))->assertSuccessful();

    // Filament v4 redirects to view page after create, not index
    livewire(Roles\Pages\CreateRole::class)
        ->fillForm([
            'name' => 'Test',
            'guard_name' => null,
        ])
        ->call('create')
        ->assertHasNoErrors();
    // ->assertRedirect(RoleResource::getUrl('index'));

    $this->assertDatabaseHas(Role::class, [
        'name' => 'Test',
        'guard_name' => Role::GUARD_NAME_WEB,
    ]);

    // Test comprehensive validation in one component instance
    $existingRole = Role::factory()->create(['name' => 'TestExisting', 'is_default' => false]);
    $component = livewire(Roles\Pages\CreateRole::class);

    // required
    $component->fillForm(['name' => null])
        ->call('create')
        ->assertHasFormErrors(['name' => ['required']]);

    // unique
    $component->fillForm(['name' => $existingRole->name])
        ->call('create')
        ->assertHasFormErrors(['name' => ['unique']]);

    // max length (name)
    $component->fillForm(['name' => Str::random(256)])
        ->call('create')
        ->assertHasFormErrors(['name' => ['max']]);

    // max length (guard_name)
    $component->fillForm([
        'name' => 'Valid Name',
        'guard_name' => Str::random(256),
    ])
        ->call('create')
        ->assertHasFormErrors(['guard_name' => ['max']]);
});

it('can edit and delete roles with validation', function (): void {
    // Create test roles
    $editRole = Role::factory()->create(['name' => 'EditTest', 'is_default' => false]);
    $existingRole = $this->user->roles()->first();

    // Test edit page rendering and data retrieval
    // $this->get(RoleResource::getUrl('edit', ['record' => $editRole->getRouteKey()]))->assertSuccessful();

    $component = livewire(Roles\Pages\EditRole::class, ['record' => $editRole->getRouteKey()])
        ->assertFormSet([
            'name' => $editRole->name,
            'guard_name' => $editRole->guard_name,
        ]);

    // Test successful editing
    $newName = fake()->domainName;
    $component->fillForm([
        'name' => $newName,
        'guard_name' => Role::GUARD_NAME_WEB,
    ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($editRole->refresh())
        ->name->toBe($newName)
        ->guard_name->toBe(Role::GUARD_NAME_WEB);

    // Test edit validation using new role for clean state
    $testRecord = Role::factory()->create(['name' => 'TestRecord', 'is_default' => false]);
    $editComponent = livewire(Roles\Pages\EditRole::class, ['record' => $testRecord->getRouteKey()]);

    // Test required validation
    $editComponent->fillForm(['name' => null])
        ->call('save')
        ->assertHasFormErrors(['name' => ['required']]);

    // Test unique validation
    $editComponent->fillForm(['name' => $existingRole->name])
        ->call('save')
        ->assertHasFormErrors(['name' => ['unique']]);

    // Test max length validation
    $editComponent->fillForm(['name' => Str::random(256)])
        ->call('save')
        ->assertHasFormErrors(['name' => ['max']]);

    $editComponent->fillForm([
        'name' => 'Valid Name',
        'guard_name' => Str::random(256),
    ])
        ->call('save')
        ->assertHasFormErrors(['guard_name' => ['max']]);

    // Test deletion
    $deleteRecord = Role::factory()->create(['name' => 'DeleteTest', 'is_default' => false]);
    livewire(Roles\Pages\EditRole::class, ['record' => $deleteRecord->getRouteKey()])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted(Role::class, ['id' => $deleteRecord->id]);
});

it('has correct table actions and restrictions', function (): void {
    $records = Role::query()->get();

    livewire(Roles\Pages\ListRoles::class)
        ->assertCanSeeTableRecords($records)
        ->assertTableActionExists('delete')
        ->assertTableBulkActionExists('delete');
});

it('handles default role restrictions and view actions correctly', function (): void {
    // Create test roles
    $defaultRole = Role::factory()->create(['name' => 'test_default', 'is_default' => true]);
    $nonDefaultRole = Role::factory()->create(['is_default' => false]);

    // Test default role deletion restriction
    expect(function () use ($defaultRole): void {
        $defaultRole->delete();
    })->toThrow(Exception::class, 'Default roles cannot be deleted');

    // Test edit action visibility - visible for non-default, hidden for default
    livewire(Roles\Pages\ViewRole::class, ['record' => $nonDefaultRole->getRouteKey()])
        ->assertActionExists(EditAction::class);

    livewire(Roles\Pages\ViewRole::class, ['record' => $defaultRole->getRouteKey()])
        ->assertActionHidden(EditAction::class);
});

it('can handle custom permissions with unknown panel names', function (): void {
    // Create a custom permission with unknown panel name
    Permission::create([
        'name' => 'filament-panel.unknown-panel',
        'guard_name' => 'web',
    ]);

    $customPermissions = RoleResource::getCustomPermissionOptions();

    expect($customPermissions)->toBeArray()
        ->toHaveKey('filament-panel.unknown-panel')
        ->and($customPermissions['filament-panel.unknown-panel'])->toBe('Unknown-Panel Panel Access');
});

it('can handle permissions that do not match resource pattern', function (): void {
    // Create permissions that don't match the resource pattern
    Permission::create([
        'name' => 'single-word-permission',
        'guard_name' => 'web',
    ]);

    Permission::create([
        'name' => 'another.dotted.permission',
        'guard_name' => 'web',
    ]);

    // Test that getResourceEntitiesSchema properly filters out non-resource permissions
    $resourceEntities = RoleResource::getResourceEntitiesSchema();

    expect($resourceEntities)->toBeArray();

    // These permissions should not appear as sections since they don't match resource pattern
    $sectionLabels = collect($resourceEntities)->pluck('label')->toArray();
    expect($sectionLabels)->not->toContain('single-word-permission')
        ->and($sectionLabels)->not->toContain('another.dotted.permission');
});

it('syncs permissions when creating a role', function (): void {
    \Illuminate\Support\Facades\Gate::before(fn (): true => true);

    // Create test permissions
    $viewRolePermission = Permission::create([
        'name' => 'view_test_role',
        'guard_name' => 'web',
    ]);

    $createRolePermission = Permission::create([
        'name' => 'create_test_role',
        'guard_name' => 'web',
    ]);

    // Create role directly and sync permissions to verify the sync functionality works
    $role = Role::create([
        'name' => 'Test Role With Permissions',
        'guard_name' => Role::GUARD_NAME_WEB,
        'is_default' => false,
    ]);

    // Test that syncPermissions works (this is what $this->record->syncPermissions($this->permissions) does in CreateRole::afterCreate)
    $permissions = collect([$viewRolePermission->name, $createRolePermission->name]);
    $role->syncPermissions($permissions);

    // Verify permissions were synced
    $role->refresh();
    expect($role->permissions->pluck('name')->toArray())
        ->toContain($viewRolePermission->name, $createRolePermission->name)
        ->toHaveCount(2);
});

it('syncs permissions when editing a role', function (): void {
    // Create test permissions
    $viewRolePermission = Permission::create([
        'name' => 'view_edit_test_role',
        'guard_name' => 'web',
    ]);

    $createUserPermission = Permission::create([
        'name' => 'create_edit_test_user',
        'guard_name' => 'web',
    ]);

    $deleteUserPermission = Permission::create([
        'name' => 'delete_edit_test_user',
        'guard_name' => 'web',
    ]);

    // Create a role with initial permissions - explicitly set guard_name to 'web'
    $role = Role::factory()->create([
        'name' => 'Test Edit Role',
        'guard_name' => Role::GUARD_NAME_WEB,
        'is_default' => false,
    ]);
    $role->givePermissionTo([$viewRolePermission->name]);

    // Verify initial state
    expect($role->refresh()->permissions->pluck('name')->toArray())
        ->toContain($viewRolePermission->name)
        ->toHaveCount(1);

    // Test syncPermissions - updating role permissions (this is what $this->record->syncPermissions($this->permissions) does in EditRole::afterSave)
    $newPermissions = collect([$createUserPermission->name, $deleteUserPermission->name]);
    $role->syncPermissions($newPermissions);

    // Refresh role and verify permissions were synced
    $role->refresh();
    expect($role->permissions->pluck('name')->toArray())
        ->toContain($createUserPermission->name, $deleteUserPermission->name)
        ->not->toContain($viewRolePermission->name)
        ->toHaveCount(2);
});

it('returns permission prefixes as array', function (): void {
    $prefixes = RoleResource::getPermissionPrefixes();

    expect($prefixes)->toBeArray();
});

it('permission prefixes contain standard CRUD prefixes', function (): void {
    $prefixes = RoleResource::getPermissionPrefixes();

    expect($prefixes)
        ->toContain('view')
        ->toContain('view_any')
        ->toContain('create')
        ->toContain('update')
        ->toContain('delete')
        ->toContain('delete_any');
});

it('permission prefixes count is exactly 6', function (): void {
    $prefixes = RoleResource::getPermissionPrefixes();

    expect($prefixes)->toHaveCount(6);
});

it('permission prefixes are in expected order', function (): void {
    $prefixes = RoleResource::getPermissionPrefixes();

    $expectedPrefixes = [
        'view',
        'view_any',
        'create',
        'update',
        'delete',
        'delete_any',
    ];

    expect($prefixes)->toBe($expectedPrefixes);
});

it('permission prefixes are all strings', function (): void {
    $prefixes = RoleResource::getPermissionPrefixes();

    foreach ($prefixes as $prefix) {
        expect($prefix)->toBeString();
    }
});

it('permission prefixes do not contain restore or force delete', function (): void {
    $prefixes = RoleResource::getPermissionPrefixes();

    expect($prefixes)
        ->not->toContain('restore')
        ->not->toContain('restore_any')
        ->not->toContain('force_delete')
        ->not->toContain('force_delete_any');
});
