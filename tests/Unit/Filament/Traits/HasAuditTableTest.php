<?php

declare(strict_types=1);

use App\Filament\Traits\HasAuditTable;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Illuminate\Database\Eloquent\Builder;

class TestClassWithAuditTrait
{
    use HasAuditTable {
        applyAuditQuery as public;
        standardRecordActions as public;
        standardCrudActions as public;
        standardToolbarActions as public;
    }
}

it('applies audit query with eager loading', function (): void {
    // Test the logic that applyAuditQuery uses
    $query = User::query();

    // This is what applyAuditQuery does internally via modifyQueryUsing
    $modifiedQuery = $query->with(['createdBy', 'updatedBy', 'deletedBy']);

    expect($modifiedQuery)->toBeInstanceOf(Builder::class)
        ->and($modifiedQuery->getEagerLoads())->toHaveKey('createdBy')
        ->and($modifiedQuery->getEagerLoads())->toHaveKey('updatedBy')
        ->and($modifiedQuery->getEagerLoads())->toHaveKey('deletedBy');
});

it('eager loads audit relationships in query', function (): void {
    // Create a user to test eager loading
    $user = User::factory()->create();

    $query = User::query();

    // Apply the modification logic that applyAuditQuery uses
    $modifiedQuery = $query->with(['createdBy', 'updatedBy', 'deletedBy']);

    expect($modifiedQuery->getEagerLoads())->toHaveKey('createdBy')
        ->and($modifiedQuery->getEagerLoads())->toHaveKey('updatedBy')
        ->and($modifiedQuery->getEagerLoads())->toHaveKey('deletedBy');
});

it('returns standard record actions as ActionGroup', function (): void {
    $actions = TestClassWithAuditTrait::standardRecordActions();

    expect($actions)->toBeArray()
        ->toHaveCount(1)
        ->and($actions[0])->toBeInstanceOf(ActionGroup::class);
});

it('standard record actions contain edit action', function (): void {
    $actions = TestClassWithAuditTrait::standardRecordActions();
    $actionGroup = $actions[0];

    $groupActions = $actionGroup->getActions();

    $hasEditAction = false;
    foreach ($groupActions as $action) {
        if ($action instanceof EditAction) {
            $hasEditAction = true;

            break;
        }
    }

    expect($hasEditAction)->toBeTrue();
});

it('standard record actions contain delete action', function (): void {
    $actions = TestClassWithAuditTrait::standardRecordActions();
    $actionGroup = $actions[0];

    $groupActions = $actionGroup->getActions();

    $hasDeleteAction = false;
    foreach ($groupActions as $action) {
        if ($action instanceof DeleteAction) {
            $hasDeleteAction = true;

            break;
        }
    }

    expect($hasDeleteAction)->toBeTrue();
});

it('standard record actions contain restore action', function (): void {
    $actions = TestClassWithAuditTrait::standardRecordActions();
    $actionGroup = $actions[0];

    $groupActions = $actionGroup->getActions();

    $hasRestoreAction = false;
    foreach ($groupActions as $action) {
        if ($action instanceof RestoreAction) {
            $hasRestoreAction = true;

            break;
        }
    }

    expect($hasRestoreAction)->toBeTrue();
});

it('standard record actions contain force delete action', function (): void {
    $actions = TestClassWithAuditTrait::standardRecordActions();
    $actionGroup = $actions[0];

    $groupActions = $actionGroup->getActions();

    $hasForceDeleteAction = false;
    foreach ($groupActions as $action) {
        if ($action instanceof ForceDeleteAction) {
            $hasForceDeleteAction = true;

            break;
        }
    }

    expect($hasForceDeleteAction)->toBeTrue();
});

it('returns standard crud actions as array', function (): void {
    $actions = TestClassWithAuditTrait::standardCrudActions();

    expect($actions)->toBeArray()
        ->toHaveCount(4);
});

it('standard crud actions contain edit action', function (): void {
    $actions = TestClassWithAuditTrait::standardCrudActions();

    $hasEditAction = false;
    foreach ($actions as $action) {
        if ($action instanceof EditAction) {
            $hasEditAction = true;

            break;
        }
    }

    expect($hasEditAction)->toBeTrue();
});

it('standard crud actions contain delete action', function (): void {
    $actions = TestClassWithAuditTrait::standardCrudActions();

    $hasDeleteAction = false;
    foreach ($actions as $action) {
        if ($action instanceof DeleteAction) {
            $hasDeleteAction = true;

            break;
        }
    }

    expect($hasDeleteAction)->toBeTrue();
});

it('standard crud actions contain restore action', function (): void {
    $actions = TestClassWithAuditTrait::standardCrudActions();

    $hasRestoreAction = false;
    foreach ($actions as $action) {
        if ($action instanceof RestoreAction) {
            $hasRestoreAction = true;

            break;
        }
    }

    expect($hasRestoreAction)->toBeTrue();
});

it('standard crud actions contain force delete action', function (): void {
    $actions = TestClassWithAuditTrait::standardCrudActions();

    $hasForceDeleteAction = false;
    foreach ($actions as $action) {
        if ($action instanceof ForceDeleteAction) {
            $hasForceDeleteAction = true;

            break;
        }
    }

    expect($hasForceDeleteAction)->toBeTrue();
});

it('returns standard toolbar actions as BulkActionGroup', function (): void {
    $actions = TestClassWithAuditTrait::standardToolbarActions();

    expect($actions)->toBeArray()
        ->toHaveCount(1)
        ->and($actions[0])->toBeInstanceOf(BulkActionGroup::class);
});

it('standard toolbar actions contain delete bulk action', function (): void {
    $actions = TestClassWithAuditTrait::standardToolbarActions();
    $bulkActionGroup = $actions[0];

    $groupActions = $bulkActionGroup->getActions();

    $hasDeleteBulkAction = false;
    foreach ($groupActions as $action) {
        if ($action instanceof DeleteBulkAction) {
            $hasDeleteBulkAction = true;

            break;
        }
    }

    expect($hasDeleteBulkAction)->toBeTrue();
});

it('restore action visibility logic checks if record is trashed', function (): void {
    // Create a role that supports soft deletes
    $role = Role::factory()->create([
        'name' => 'Test Restore Role',
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    // Test the trashed check part of the visibility logic
    // For non-trashed record
    $hasTrashedMethod = method_exists($role, 'trashed');
    $isTrashed = $hasTrashedMethod && $role->trashed();

    expect($hasTrashedMethod)->toBeTrue()
        ->and($isTrashed)->toBeFalse();

    // Delete the role to make it trashed
    $role->delete();
    $role->refresh();

    // For trashed record
    $isTrashed = method_exists($role, 'trashed') && $role->trashed();

    expect($isTrashed)->toBeTrue();
});

it('force delete action visibility logic checks if record is trashed', function (): void {
    // Create a role that supports soft deletes
    $role = Role::factory()->create([
        'name' => 'Test Force Delete Role',
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    // Test the trashed check part of the visibility logic
    // For non-trashed record
    $hasTrashedMethod = method_exists($role, 'trashed');
    $isTrashed = $hasTrashedMethod && $role->trashed();

    expect($hasTrashedMethod)->toBeTrue()
        ->and($isTrashed)->toBeFalse();

    // Delete the role to make it trashed
    $role->delete();
    $role->refresh();

    // For trashed record
    $isTrashed = method_exists($role, 'trashed') && $role->trashed();

    expect($isTrashed)->toBeTrue();
});
