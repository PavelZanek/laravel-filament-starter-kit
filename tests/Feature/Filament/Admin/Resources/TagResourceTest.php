<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Pages;

use App\Filament\Admin\Resources\TagResource;
use App\Models\Content\Tag;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('returns correct navigation labels and groups', function (): void {
    expect(TagResource::getNavigationLabel())->toBeString()
        ->and(TagResource::getNavigationGroup())->toBeString()
        ->and(TagResource::getBreadcrumb())->toBeString();
});

it('returns proper query without global scopes', function (string $column): void {
    $query = TagResource::getEloquentQuery();
    expect($query)->toBeInstanceOf(Builder::class);

    $columns = $query->getQuery()->getColumns();
    expect($columns)->not->toHaveKey($column);
})->with(['deleted_at']);

it('provides relations and pages arrays', function (): void {
    expect(TagResource::getRelations())->toBeArray()
        ->and(TagResource::getPages())->toBeArray();
});

it('can render the resource', function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    actingAs(User::factory()->withRole(Role::SUPER_ADMIN)->create());

    get(TagResource::getUrl())->assertSuccessful();
});

it('can not render the resource for disallowed user', function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    actingAs(User::factory()->withRole()->create());

    get(TagResource::getUrl())->assertForbidden();
});

it('can list records', function (): void {
    $records = Tag::factory()->count(3)->create();

    livewire(TagResource\Pages\ManageTags::class)
        ->assertCanSeeTableRecords($records);
});

it('returns correct title', function (): void {
    $list = new TagResource\Pages\ManageTags;

    expect($list->getTitle())->toBe(__('admin/tag-resource.list.title'));
});

it('dispatches scroll-to-top on page set', function (): void {
    livewire(TagResource\Pages\ManageTags::class)
        ->call('setPage', 2)
        ->assertDispatched('scroll-to-top');
});

it('has column', function (string $column): void {
    livewire(TagResource\Pages\ManageTags::class)
        ->assertTableColumnExists($column);
})->with(['name', 'created_at']);

it('can render column', function (string $column): void {
    livewire(TagResource\Pages\ManageTags::class)
        ->assertCanRenderTableColumn($column);
})->with(['name', 'created_at']);

it('can sort column', function (string $column): void {
    $records = Tag::factory(5)->create();

    livewire(TagResource\Pages\ManageTags::class)
        ->sortTable($column)
        ->assertCanSeeTableRecords($records->sortBy($column), inOrder: true)
        ->sortTable($column, 'desc')
        ->assertCanSeeTableRecords($records->sortByDesc($column), inOrder: true);
})->with(['name', 'created_at']);

it('can search column', function (string $column): void {
    $records = Tag::factory(5)->create();

    $value = $records->first()->{$column};

    livewire(TagResource\Pages\ManageTags::class)
        ->searchTable($value)
        ->assertCanSeeTableRecords($records->where($column, $value))
        ->assertCanNotSeeTableRecords($records->where($column, '!=', $value));
})->with(['name']);

it('shows correct filter indicators', function (string $filter): void {
    $createdFrom = now()->subDays(5)->toDateString();
    $createdUntil = now()->subDay()->toDateString();

    $component = livewire(TagResource\Pages\ManageTags::class)
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

it('can delete records from table', function (): void {
    $records = Tag::factory()->count(3)->create();

    livewire(TagResource\Pages\ManageTags::class)
        ->assertCanSeeTableRecords($records)
        ->callTableAction(DeleteAction::class, $records->first()->getKey());

    assertSoftDeleted($records->first());
});

it('can render the create modal', function (): void {
    livewire(TagResource\Pages\ManageTags::class)
        ->assertActionExists(CreateAction::class);
});

it('can create a record', function (): void {
    $newData = Tag::factory()->make();

    livewire(TagResource\Pages\ManageTags::class)
        ->callAction(CreateAction::class, [
            'name' => $newData->name,
            'slug' => $newData->slug,
        ])
        ->assertHasNoErrors();

    assertDatabaseHas(Tag::class, [
        'name' => $newData->name,
        'slug' => $newData->slug,
    ]);
});

it('can validate required', function (string $column): void {
    livewire(TagResource\Pages\ManageTags::class)
        ->mountAction('create')
        ->setActionData([$column => null])
        ->callMountedAction()
        ->assertHasActionErrors([$column => ['required']]);
})->with(['name', 'slug']);

it('can validate unique', function (string $column): void {
    $record = Tag::factory()->create();

    livewire(TagResource\Pages\ManageTags::class)
        ->mountAction('create')
        ->setActionData([$column => $record->slug])
        ->callMountedAction()
        ->assertHasActionErrors([$column => ['unique']]);
})->with(['slug']);

it('can validate max length', function (string $column): void {
    livewire(TagResource\Pages\ManageTags::class)
        ->mountAction('create')
        ->setActionData([$column => Str::random(256)])
        ->callMountedAction()
        ->assertHasActionErrors([$column => ['max:255']]);
})->with(['name', 'slug']);

it('can render the edit modal', function (): void {
    $record = Tag::factory()->create();

    livewire(TagResource\Pages\ManageTags::class)
        ->assertTableActionExists(EditAction::class, record: $record);
});

it('can update a record (without slug change)', function (): void {
    $record = Tag::factory()->create();
    $newData = Tag::factory()->make();

    livewire(TagResource\Pages\ManageTags::class)
        ->callTableAction(EditAction::class, $record->getKey(), [
            'name' => $newData->name,
            'slug' => $newData->slug, // disabled on edit
        ])
        ->assertHasNoTableActionErrors();

    expect($record->refresh())
        ->name->toBe($newData->name)
        ->slug->toBe($record->slug);
});

it('can validate required (edit page)', function (string $column): void {
    $record = Tag::factory()->create();

    livewire(TagResource\Pages\ManageTags::class)
        ->callTableAction(EditAction::class, $record->getKey(), [$column => null])
        ->assertHasTableActionErrors([$column => ['required']]);
})->with(['name', 'slug']);

it('can validate unique (edit page)', function (string $column): void {
    [$recordA, $recordB] = Tag::factory()->count(2)->create();

    livewire(TagResource\Pages\ManageTags::class)
        ->callTableAction(EditAction::class, $recordA->getKey(), [$column => $recordB->{$column}])
        ->assertHasTableActionErrors([$column => ['unique']]);
})->with(['slug']);

it('can validate max length (edit page)', function (string $column): void {
    $record = Tag::factory()->create();

    livewire(TagResource\Pages\ManageTags::class)
        ->callTableAction(EditAction::class, $record->getKey(), [$column => Str::random(256)])
        ->assertHasTableActionErrors([$column => ['max:255']]);
})->with(['name', 'slug']);

it('can soft delete a record', function (): void {
    $record = Tag::factory()->create();

    livewire(TagResource\Pages\ManageTags::class)
        ->callTableAction(DeleteAction::class, $record->getKey())
        ->assertHasNoTableActionErrors();

    assertSoftDeleted($record);
});
