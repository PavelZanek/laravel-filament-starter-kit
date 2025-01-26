<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Pages;

use App\Filament\Admin\Resources\CommentResource;
use App\Models\Content\Comment;
use App\Models\Content\Post;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    $this->user = User::factory()->withRole(Role::SUPER_ADMIN)->create();
    actingAs($this->user);
});

it('returns correct navigation labels and groups', function (): void {
    expect(CommentResource::getNavigationLabel())->toBeString()
        ->and(CommentResource::getNavigationGroup())->toBeString()
        ->and(CommentResource::getBreadcrumb())->toBeString();
});

it('returns proper query without global scopes', function (string $column): void {
    $query = CommentResource::getEloquentQuery();
    expect($query)->toBeInstanceOf(Builder::class);

    $columns = $query->getQuery()->getColumns();
    expect($columns)->not->toHaveKey($column);
})->with(['deleted_at']);

it('provides relations and pages arrays', function (): void {
    expect(CommentResource::getRelations())->toBeArray()
        ->and(CommentResource::getPages())->toBeArray();
});

it('can render the resource', function (): void {
    get(CommentResource::getUrl())->assertSuccessful();
});

it('can not render the resource for disallowed user', function (): void {
    $user = User::factory()->withRole()->create();
    actingAs($user);

    get(CommentResource::getUrl())->assertForbidden();
});

it('can list records', function (): void {
    $records = Comment::factory()->for(Post::factory(), 'commentable')->count(3)->create();

    livewire(CommentResource\Pages\ListComments::class)
        ->assertCanSeeTableRecords($records);
});

it('returns correct title', function (): void {
    $listUsers = new CommentResource\Pages\ListComments;

    expect($listUsers->getTitle())->toBe(__('admin/comment-resource.list.title'));
});

it('dispatches scroll-to-top on page set', function (): void {
    livewire(CommentResource\Pages\ListComments::class)
        ->call('setPage', 2)
        ->assertDispatched('scroll-to-top');
});

it('has column', function (string $column): void {
    livewire(CommentResource\Pages\ListComments::class)
        ->assertTableColumnExists($column);
})->with(['commentable_id', 'commentable_type', 'user.email', 'created_at']);

it('can render column', function (string $column): void {
    livewire(CommentResource\Pages\ListComments::class)
        ->assertCanRenderTableColumn($column);
})->with(['commentable_id', 'commentable_type', 'user.email', 'created_at']);

it('can sort column', function (string $column): void {
    $records = Comment::factory()->for(Post::factory(), 'commentable')->count(3)->create();

    livewire(CommentResource\Pages\ListComments::class)
        ->sortTable($column)
        ->assertCanSeeTableRecords($records->sortBy($column), inOrder: true)
        ->sortTable($column, 'desc')
        ->assertCanSeeTableRecords($records->sortByDesc($column), inOrder: true);
})->with(['user.email', 'created_at']);

it('shows correct filter indicators', function (string $filter): void {
    $createdFrom = now()->subDays(5)->toDateString();
    $createdUntil = now()->subDay()->toDateString();

    $component = livewire(CommentResource\Pages\ListComments::class)
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
    $records = Comment::factory()->for(Post::factory(), 'commentable')->count(3)->create();

    livewire(CommentResource\Pages\ListComments::class)
        ->assertCanSeeTableRecords($records)
        ->assertTableActionExists(DeleteAction::class, record: $records->first()->getKey())
        ->assertTableBulkActionExists(DeleteBulkAction::class);
});

it('can render the create page', function (): void {
    get(CommentResource::getUrl('create'))->assertSuccessful();
});

it('can not render the create page for disallowed user', function (): void {
    $user = User::factory()->withRole()->create();
    actingAs($user);

    get(CommentResource::getUrl('create'))->assertForbidden();
});

it('can create a record', function (): void {
    $commentable = Post::factory()->create();
    $newData = Comment::factory()->for($commentable, 'commentable')->make();

    livewire(CommentResource\Pages\CreateComment::class)
        ->fillForm([
            'user_id' => $newData->user_id,
            'content' => $newData->content,
            'commentable_type' => $commentable->getMorphClass(),
            'commentable_id' => $commentable->getKey(),
        ])
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(CommentResource::getUrl());

    assertDatabaseHas(Comment::class, [
        'user_id' => $newData->user_id,
        'content' => $newData->content,
        'commentable_type' => $commentable->getMorphClass(),
        'commentable_id' => $commentable->getKey(),
    ]);
});

it('can validate required', function (string $column): void {
    livewire(CommentResource\Pages\CreateComment::class)
        ->fillForm([$column => null])
        ->assertActionExists('create')
        ->call('create')
        ->assertHasFormErrors([$column => ['required']]);
})->with(['user_id', 'content', 'commentable_type']);

it('can validate max length of comment content', function (): void {
    livewire(CommentResource\Pages\CreateComment::class)
        ->fillForm(['content' => Str::random(1001)])
        ->assertActionExists('create')
        ->call('create')
        ->assertHasFormErrors(['content' => ['max:1000']]);
});

it('can render the edit page', function (): void {
    $record = Comment::factory()->for(Post::factory(), 'commentable')->create();

    get(CommentResource::getUrl('edit', [
        'record' => $record->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve record data', function (): void {
    $record = Comment::factory()->for(Post::factory(), 'commentable')->create();

    livewire(CommentResource\Pages\EditComment::class, [
        'record' => $record->getRouteKey(),
    ])
        ->assertFormSet([
            'user_id' => $record->user_id,
            'content' => $record->content,
            'commentable_id' => $record->commentable_id,
            'commentable_type' => $record->commentable_type,
        ]);
});

it('can update a record', function (): void {
    $newData = Comment::factory()->for(Post::factory(), 'commentable')->make();
    $record = Comment::factory()->for(Post::factory(), 'commentable')->create();

    $commentable = Post::factory()->create();

    livewire(CommentResource\Pages\EditComment::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([
            'user_id' => $newData->user_id,
            'content' => $newData->content,
            'commentable_type' => $commentable->getMorphClass(),
            'commentable_id' => $commentable->getKey(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->user_id->toBe($newData->user_id)
        ->content->toBe($newData->content)
        ->commentable_id->toBe($commentable->getKey())
        ->commentable_type->toBe($commentable->getMorphClass());
});

it('can validate required (edit page)', function (string $column): void {
    $record = Comment::factory()->for(Post::factory(), 'commentable')->create();

    livewire(CommentResource\Pages\EditComment::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([$column => null])
        ->assertActionExists('save')
        ->call('save')
        ->assertHasFormErrors([$column => ['required']]);
})->with(['user_id', 'content', 'commentable_id', 'commentable_type']);

it('can validate max length of comment content (edit page)', function (): void {
    $record = Comment::factory()->for(Post::factory(), 'commentable')->create();

    livewire(CommentResource\Pages\EditComment::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm(['content' => Str::random(1001)])
        ->assertActionExists('save')
        ->call('save')
        ->assertHasFormErrors(['content' => ['max:1000']]);
});

it('can soft delete a record', function (): void {
    $record = Comment::factory()->for(Post::factory(), 'commentable')->create();

    livewire(CommentResource\Pages\EditComment::class, [
        'record' => $record->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    assertSoftDeleted($record);
});
