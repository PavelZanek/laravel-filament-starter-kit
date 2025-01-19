<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    $this->user = User::factory()->withRole(Role::SUPER_ADMIN)->create();
    actingAs($this->user);
});

it('returns correct navigation labels and groups', function (): void {
    expect(UserResource::getNavigationLabel())->toBeString()
        ->and(UserResource::getNavigationGroup())->toBeString()
        ->and(UserResource::getBreadcrumb())->toBeString();
});

it('returns proper query without global scopes', function (string $column): void {
    $query = UserResource::getEloquentQuery();
    expect($query)->toBeInstanceOf(Builder::class);

    $columns = $query->getQuery()->getColumns();
    expect($columns)->not->toHaveKey($column);
})->with(['deleted_at']);

it('provides relations and pages arrays', function (): void {
    expect(UserResource::getRelations())->toBeArray()
        ->and(UserResource::getPages())->toBeArray();
});

it('can render the resource', function (): void {
    $this->get(UserResource::getUrl())->assertSuccessful();
});

it('can not render the resource for disallowed user', function (): void {
    $user = User::factory()->withRole()->create();
    actingAs($user);

    $this->get(UserResource::getUrl())->assertForbidden();
});

it('can list records', function (): void {
    $users = User::factory()->count(5)->withWorkspaces()->withRole()->create();

    $users->add($this->user);

    livewire(UserResource\Pages\ListUsers::class)
        ->assertCanSeeTableRecords($users);
});

it('returns correct title', function (): void {
    $listUsers = new UserResource\Pages\ListUsers;

    expect($listUsers->getTitle())->toBe(__('admin/user-resource.list.title'));
});

it('dispatches scroll-to-top on page set', function (): void {
    livewire(UserResource\Pages\ListUsers::class)
        ->call('setPage', 2)
        ->assertDispatched('scroll-to-top');
});

it('has tabs', function (string $tab): void {
    User::factory()->withRole($tab === 'all' ? Role::AUTHENTICATED : $tab)->count(3)->create();

    $listUsers = new UserResource\Pages\ListUsers;

    $tabs = $listUsers->getTabs();

    expect($tabs)->toBeArray()
        ->and($tabs)->toHaveKey('all');

    $tabSlug = str($tab)->slug()->toString();

    expect($tabs)->toHaveKey($tabSlug)
        ->and($tabs[$tabSlug])->toBeInstanceOf(Tab::class)
        ->and($tabs[$tabSlug]->getLabel())->toBe($tab === 'all' ? __('common.all') : Role::ROLES[$tab])
        ->and($tabs[$tabSlug]->getBadge())->toBe(
            $tab === 'all' ? User::query()->count() : User::query()->role($tab)->count() // @phpstan-ignore-line
        );
})->with(['all', Role::SUPER_ADMIN, Role::ADMIN, Role::AUTHENTICATED]);

it('has column', function (string $column): void {
    livewire(UserResource\Pages\ListUsers::class)
        ->assertTableColumnExists($column);
})->with(['name', 'email', 'roles.name', 'created_at']);

it('can render column', function (string $column): void {
    livewire(UserResource\Pages\ListUsers::class)
        ->assertCanRenderTableColumn($column);
})->with(['name', 'email', 'roles.name', 'created_at']);

it('can sort column', function (string $column): void {
    $records = User::factory(5)->withRole()->create();

    livewire(UserResource\Pages\ListUsers::class)
        ->sortTable($column)
        ->assertCanSeeTableRecords($records->sortBy($column), inOrder: true)
        ->sortTable($column, 'desc')
        ->assertCanSeeTableRecords($records->sortByDesc($column), inOrder: true);
})->with(['name', 'email', 'roles.name',  'created_at']);

it('can search column', function (string $column): void {
    $records = User::factory(5)->withRole()->create();

    $value = $records->first()->{$column};

    livewire(UserResource\Pages\ListUsers::class)
        ->searchTable($value)
        ->assertCanSeeTableRecords($records->where($column, $value))
        ->assertCanNotSeeTableRecords($records->where($column, '!=', $value));
})->with(['name', 'email', 'roles.name']);

it('can filter records', function (string $filter): void {
    User::factory()->withRole()->count(4)->create();
    User::factory()->withRole()->unverified()->count(5)->create();

    $users = User::all();

    livewire(UserResource\Pages\ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->filterTable($filter)
        ->assertCanSeeTableRecords($users->whereNotNull($filter))
        ->assertCanNotSeeTableRecords($users->whereNull($filter));
})->with(['email_verified_at']);

it('shows correct filter indicators', function (string $filter): void {
    $createdFrom = now()->subDays(5)->toDateString();
    $createdUntil = now()->subDay()->toDateString();

    $component = livewire(UserResource\Pages\ListUsers::class)
        ->filterTable($filter, [
            'created_from' => $createdFrom,
            'created_until' => $createdUntil,
        ]);

    $indicators = $component->instance()->getTableFilters()[$filter]->getIndicatorUsing()([
        'created_from' => $createdFrom,
        'created_until' => $createdUntil,
    ]);

    expect($indicators)->not->toBeEmpty();
})->with(['created_at'])->skip('TODO: Implement filter indicators');

it('can not delete records from table', function (): void {
    User::factory()->withRole()->count(4)->create();

    $users = User::all();

    livewire(UserResource\Pages\ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->assertActionDoesNotExist(DeleteAction::class)
        ->assertTableBulkActionDoesNotExist(DeleteBulkAction::class);
});

it('can render the create page', function (): void {
    $this->get(UserResource::getUrl('create'))->assertSuccessful();
});

it('can not render the create page for disallowed user', function (): void {
    $user = User::factory()->withRole()->create();
    actingAs($user);

    $this->get(UserResource::getUrl('create'))->assertForbidden();
});

it('can create a record', function (): void {
    $newData = User::factory()->make();
    $role = Role::factory()->create(['name' => Role::ADMIN]);

    livewire(UserResource\Pages\CreateUser::class)
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
            'password' => 'password',
            'roles' => [$role->id],
        ])
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(UserResource::getUrl());

    $this->assertDatabaseHas(User::class, [
        'name' => $newData->name,
        'email' => $newData->email,
    ]);
});

it('can validate required', function (string $column): void {
    livewire(UserResource\Pages\CreateUser::class)
        ->fillForm([$column => null])
        ->assertActionExists('create')
        ->call('create')
        ->assertHasFormErrors([$column => ['required']]);
})->with(['name', 'email', 'roles']);

it('can validate unique', function (string $column): void {
    $record = User::factory()->create();

    livewire(UserResource\Pages\CreateUser::class)
        ->fillForm(['email' => $record->email])
        ->assertActionExists('create')
        ->call('create')
        ->assertHasFormErrors([$column => ['unique']]);
})->with(['email']);

it('can validate email', function (string $column): void {
    livewire(UserResource\Pages\CreateUser::class)
        ->fillForm(['email' => Str::random()])
        ->assertActionExists('create')
        ->call('create')
        ->assertHasFormErrors([$column => ['email']]);
})->with(['email']);

it('can validate max length', function (string $column): void {
    livewire(UserResource\Pages\CreateUser::class)
        ->fillForm([$column => Str::random(256)])
        ->assertActionExists('create')
        ->call('create')
        ->assertHasFormErrors([$column => ['max:255']]);
})->with(['name', 'email']);

it('can render the edit page', function (): void {
    $this->get(UserResource::getUrl('edit', [
        'record' => $this->user->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve record data', function (): void {
    livewire(UserResource\Pages\EditUser::class, [
        'record' => $this->user->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);
});

it('can update a record', function (): void {
    $newData = User::factory()->make();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $this->user->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
            'email' => $newData->email,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->user->refresh())
        ->name->toBe($newData->name)
        ->email->toBe($newData->email);
});

it('can validate required (edit page)', function (string $column): void {
    $user = User::factory()->withRole()->create();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->fillForm([$column => null])
        ->assertActionExists('save')
        ->call('save')
        ->assertHasFormErrors([$column => ['required']]);
})->with(['name', 'email', 'roles']);

it('can validate unique (edit page)', function (string $column): void {
    $record = User::factory()->create();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm(['email' => $this->user->email])
        ->assertActionExists('save')
        ->call('save')
        ->assertHasFormErrors([$column => ['unique']]);
})->with(['email']);

it('can validate email (edit page)', function (string $column): void {
    livewire(UserResource\Pages\EditUser::class, [
        'record' => $this->user->getRouteKey(),
    ])
        ->fillForm(['email' => Str::random()])
        ->assertActionExists('save')
        ->call('save')
        ->assertHasFormErrors([$column => ['email']]);
})->with(['email']);

it('can validate max length (edit page)', function (string $column): void {
    livewire(UserResource\Pages\EditUser::class, [
        'record' => $this->user->getRouteKey(),
    ])
        ->fillForm([$column => Str::random(256)])
        ->assertActionExists('save')
        ->call('save')
        ->assertHasFormErrors([$column => ['max:255']]);
})->with(['name', 'email']);

it('can soft delete a record', function (): void {
    $user = User::factory()->withRole()->create();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    $this->assertSoftDeleted($user);
});

it('can not delete super_admin user', function (): void {
    livewire(UserResource\Pages\EditUser::class, [
        'record' => $this->user->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);
});

it('can change user password via changePassword action', function (): void {
    $user = User::factory()->withRole(Role::ADMIN)->create();
    $newPassword = 'NewPassword!123';

    livewire(UserResource\Pages\ListUsers::class)
        ->callTableAction('changePassword', $user->getKey(), [
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword,
        ])
        ->assertHasNoTableActionErrors();

    expect(Hash::check($newPassword, $user->fresh()->password))->toBeTrue();
});

it('can change user role via changeRole action', function (): void {
    $user = User::factory()->withRole(Role::ADMIN)->create();
    $newRole = Role::factory()->create(['name' => Role::AUTHENTICATED]);

    livewire(UserResource\Pages\ListUsers::class)
        ->callTableAction('changeRole', $user->getKey(), [
            'new_role' => $newRole->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect($user->refresh())
        ->roles->first()->name->toBe($newRole->name);
});
