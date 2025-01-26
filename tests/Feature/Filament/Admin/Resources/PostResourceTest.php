<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Pages;

use App\Filament\Admin\Resources\PostResource;
use App\Filament\Admin\Resources\PostResource\Pages\ViewPost;
use App\Models\Content\Category;
use App\Models\Content\Comment;
use App\Models\Content\Post;
use App\Models\Content\Tag;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
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
    expect(PostResource::getNavigationLabel())->toBeString()
        // ->and(PostResource::getNavigationGroup())->toBeString()
        ->and(PostResource::getBreadcrumb())->toBeString();
});

it('returns proper query without global scopes', function (string $column): void {
    $query = PostResource::getEloquentQuery();
    expect($query)->toBeInstanceOf(Builder::class);

    $columns = $query->getQuery()->getColumns();
    expect($columns)->not->toHaveKey($column);
})->with(['deleted_at']);

it('provides relations and pages arrays', function (): void {
    expect(PostResource::getRelations())->toBeArray()
        ->and(PostResource::getPages())->toBeArray();
});

it('can render the resource', function (): void {
    get(PostResource::getUrl())->assertSuccessful();
});

it('can not render the resource for disallowed user', function (): void {
    $user = User::factory()->withRole()->create();
    actingAs($user);

    get(PostResource::getUrl())->assertForbidden();
});

it('can list records', function (): void {
    $records = Post::factory()->count(3)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($records);
});

it('returns correct title', function (): void {
    $listUsers = new PostResource\Pages\ListPosts;

    expect($listUsers->getTitle())->toBe(__('admin/post-resource.list.title'));
});

it('dispatches scroll-to-top on page set', function (): void {
    livewire(PostResource\Pages\ListPosts::class)
        ->call('setPage', 2)
        ->assertDispatched('scroll-to-top');
});

it('has column', function (string $column): void {
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableColumnExists($column);
})->with(['name', 'categories.name']);

it('can render column', function (string $column): void {
    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanRenderTableColumn($column);
})->with(['name', 'categories.name']);

it('can sort column', function (string $column): void {
    $records = Post::factory()->count(3)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->sortTable($column)
        ->assertCanSeeTableRecords($records->sortBy($column), inOrder: true)
        ->sortTable($column, 'desc')
        ->assertCanSeeTableRecords($records->sortByDesc($column), inOrder: true);
})->with(['name']);

it('shows correct filter indicators', function (string $filter): void {
    $createdFrom = now()->subDays(3)->toDateString();
    $createdUntil = now()->subDay()->toDateString();

    $component = livewire(PostResource\Pages\ListPosts::class)
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

it('cannot delete records from table', function (): void {
    $records = Post::factory()->count(3)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($records)
        ->assertTableActionDoesNotExist(DeleteAction::class, record: $records->first()->getKey())
        ->assertTableBulkActionDoesNotExist(DeleteBulkAction::class);
});

it('can render the view page', function (): void {
    $record = Post::factory()->create();

    get(PostResource::getUrl('view', [
        'record' => $record->getRouteKey(),
    ]))->assertSuccessful();
});

it('returns correct title on the view page', function (): void {
    $page = new ViewPost;

    expect($page->getTitle())->toBe(__('admin/post-resource.view.title'));
});

it('can render the create page', function (): void {
    get(PostResource::getUrl('create'))->assertSuccessful();
});

it('can not render the create page for disallowed user', function (): void {
    $user = User::factory()->withRole()->create();
    actingAs($user);

    get(PostResource::getUrl('create'))->assertForbidden();
});

it('can create a record', function (): void {
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\CreatePost::class)
        ->fillForm([
            'name' => $newData->name,
            'slug' => $newData->slug,
        ])
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(PostResource::getUrl());

    assertDatabaseHas(Post::class, [
        'name' => $newData->name,
        'slug' => $newData->slug,
    ]);
});

it('can validate required', function (string $column): void {
    livewire(PostResource\Pages\CreatePost::class)
        ->fillForm([$column => null])
        ->assertActionExists('create')
        ->call('create')
        ->assertHasFormErrors([$column => ['required']]);
})->with(['name', 'slug']);

it('can validate max length', function (string $column): void {
    livewire(PostResource\Pages\CreatePost::class)
        ->fillForm([$column => Str::random(256)])
        ->assertActionExists('create')
        ->call('create')
        ->assertHasFormErrors([$column => ['max:255']]);
})->with(['name', 'slug']);

it('can render the edit page', function (): void {
    $record = Post::factory()->create();

    get(PostResource::getUrl('edit', [
        'record' => $record->getRouteKey(),
    ]))->assertSuccessful();
});

it('can retrieve record data', function (): void {
    $record = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $record->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $record->name,
            'slug' => $record->slug,
        ]);
});

it('can update a record', function (): void {
    $newData = Post::factory()->make();
    $record = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newData->name,
            'slug' => $newData->slug,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh())
        ->name->toBe($newData->name)
        ->slug->toBe($record->slug); // slug is disabled on edit
});

it('can validate required (edit page)', function (string $column): void {
    $record = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([$column => null])
        ->assertActionExists('save')
        ->call('save')
        ->assertHasFormErrors([$column => ['required']]);
})->with(['name', 'slug']);

it('can validate max length (edit page)', function (string $column): void {
    $record = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $record->getRouteKey(),
    ])
        ->fillForm([$column => Str::random(256)])
        ->assertActionExists('save')
        ->call('save')
        ->assertHasFormErrors([$column => ['max:255']]);
})->with(['name', 'slug']);

it('can soft delete a record', function (): void {
    $record = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $record->getRouteKey(),
    ])
        ->callAction(DeleteAction::class);

    assertSoftDeleted($record);
});

it('can render authors relation manager', function (): void {
    $record = Post::factory()
        ->hasAttached(User::factory()->count(3), relationship: 'authors')
        ->create();

    livewire(PostResource\RelationManagers\AuthorsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($record->authors);
});

it('can update a record in authors relation manager', function (): void {
    $record = Post::factory()
        ->hasAttached(User::factory()->count(3), relationship: 'authors')
        ->create();

    livewire(PostResource\RelationManagers\AuthorsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertTableActionExists(EditAction::class)
        ->callTableAction(EditAction::class, $record->authors->first()->getKey(), [
            'order' => 1,
        ])
        ->assertHasNoTableActionErrors();

    expect($record->refresh())
        ->authors->first()->pivot->order->toBe(1);
});

it('can attach an existing record in authors relation manager', function (): void {
    $record = Post::factory()->create();
    $user = User::factory()->create();

    livewire(PostResource\RelationManagers\AuthorsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->callTableAction('attach', null, [
            'recordId' => $user->getKey(),
            'order' => 1,
        ])
        ->assertHasNoTableActionErrors();

    expect($record->refresh()->authors)->toHaveCount(1)
        ->first()->pivot->order->toBe(1);
});

it('can detach a record in authors relation manager', function (): void {
    $record = Post::factory()
        ->hasAttached(User::factory(), relationship: 'authors')
        ->create();

    $author = $record->authors->first();

    livewire(PostResource\RelationManagers\AuthorsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->callTableAction('detach', $author)
        ->assertHasNoTableActionErrors();

    expect($record->refresh()->authors)->toBeEmpty();
});

it('can render comments relation manager', function (): void {
    $record = Post::factory()
        ->has(Comment::factory()->count(3))
        ->create();

    livewire(PostResource\RelationManagers\CommentsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($record->comments);
});

it('can create record in comments relation manager', function (): void {
    $record = Post::factory()->create();
    $user = User::factory()->create();
    $newData = Comment::factory()->make();

    livewire(PostResource\RelationManagers\CommentsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertTableActionExists('create')
        ->mountTableAction(CreateAction::class)
        ->setTableActionData([
            'user_id' => $user->getKey(),
            'content' => $newData->content,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors()
        ->assertSuccessful();

    expect($record->comments->first())
        ->user_id->toBe($user->getKey())
        ->content->toBe($newData->content);
});

it('validates the form inputs for records in comments relation manager', function (): void {
    $record = Post::factory()->create();

    livewire(PostResource\RelationManagers\CommentsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertTableActionExists('create')
        ->mountTableAction(CreateAction::class)
        ->setTableActionData([
            'user_id' => null,
            'content' => null,
        ])
        ->callMountedTableAction()
        ->assertHasTableActionErrors(['content']);
});

it('can update a record in comments relation manager', function (): void {
    $record = Post::factory()->create();
    $comment = Comment::factory()->for($record, 'commentable')->create();
    $newData = Comment::factory()->make();

    livewire(PostResource\RelationManagers\CommentsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertTableActionExists(EditAction::class)
        ->callTableAction(EditAction::class, $comment->getKey(), [
            'user_id' => $newData->user_id,
            'content' => $newData->content,
        ])
        ->assertHasNoTableActionErrors();

    expect($comment->refresh())
        ->user_id->toBe($newData->user_id)
        ->content->toBe($newData->content);
});

it('can delete a record in comments relation manager', function (): void {
    $record = Post::factory()->create();
    $comment = Comment::factory()->for($record, 'commentable')->create();

    livewire(PostResource\RelationManagers\CommentsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->callTableAction(DeleteAction::class, $comment)
        ->assertHasNoTableActionErrors();

    assertSoftDeleted($comment);
});

it('can render categories relation manager', function (): void {
    $record = Post::factory()
        ->has(Category::factory()->count(3))
        ->create();

    livewire(PostResource\RelationManagers\CategoriesRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($record->categories);
});

it('can create record in categories relation manager', function (): void {
    $record = Post::factory()->create();
    $newData = Category::factory()->make();

    livewire(PostResource\RelationManagers\CategoriesRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertTableActionExists('create')
        ->mountTableAction(CreateAction::class)
        ->setTableActionData([
            'name' => $newData->name,
            'slug' => $newData->slug,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors()
        ->assertSuccessful();
});

it('validates the form inputs for records in categories relation manager', function (): void {
    $record = Post::factory()->has(Category::factory())->create();
    $category = $record->categories->first();

    livewire(PostResource\RelationManagers\CategoriesRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->callTableAction(EditAction::class, $category->getKey(), [
            'name' => null,
            'slug' => null,
        ])
        ->assertHasTableActionErrors(['name', 'slug']);
});

it('can update a record in categories relation manager', function (): void {
    $record = Post::factory()->has(Category::factory())->create();
    $category = $record->categories->first();
    $newData = Category::factory()->make();

    livewire(PostResource\RelationManagers\CategoriesRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertTableActionExists(EditAction::class)
        ->callTableAction(EditAction::class, $category->getKey(), [
            'name' => $newData->name,
            'slug' => $newData->slug,
        ])
        ->assertHasNoTableActionErrors();
});

it('can attach an existing record in categories relation manager', function (): void {
    $record = Post::factory()->create();
    $category = Category::factory()->create();

    livewire(PostResource\RelationManagers\CategoriesRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->callTableAction('attach', null, [
            'recordId' => $category->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect($record->refresh()->categories)->toHaveCount(1);
});

it('can detach a record in categories relation manager', function (): void {
    $record = Post::factory()
        ->has(Category::factory())
        ->create();

    $category = $record->categories->first();

    livewire(PostResource\RelationManagers\CategoriesRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->callTableAction('detach', $category)
        ->assertHasNoTableActionErrors();

    expect($record->refresh()->categories)->toBeEmpty();
});

it('renders the correct translated titles in categories relation manager', function (): void {
    $record = Post::factory()->create();

    livewire(PostResource\RelationManagers\CategoriesRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertSee(__('admin/post-resource.relationships.categories'));
});

it('can render tags relation manager', function (): void {
    $record = Post::factory()
        ->has(Tag::factory()->count(3))
        ->create();

    livewire(PostResource\RelationManagers\TagsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($record->tags);
});

it('can create record in tags relation manager', function (): void {
    $record = Post::factory()->create();
    $newData = Tag::factory()->make();

    livewire(PostResource\RelationManagers\TagsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertTableActionExists('create')
        ->mountTableAction(CreateAction::class)
        ->setTableActionData([
            'name' => $newData->name,
            'slug' => $newData->slug,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors()
        ->assertSuccessful();
});

it('validates the form inputs for records in tags relation manager', function (): void {
    $record = Post::factory()->has(Tag::factory())->create();
    $tag = $record->tags->first();

    livewire(PostResource\RelationManagers\TagsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->callTableAction(EditAction::class, $tag->getKey(), [
            'name' => null,
            'slug' => null,
        ])
        ->assertHasTableActionErrors(['name', 'slug']);
});

it('can update a record in tags relation manager', function (): void {
    $record = Post::factory()->has(Tag::factory())->create();
    $tag = $record->tags->first();
    $newData = Tag::factory()->make();

    livewire(PostResource\RelationManagers\TagsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertTableActionExists(EditAction::class)
        ->callTableAction(EditAction::class, $tag->getKey(), [
            'name' => $newData->name,
            'slug' => $newData->slug,
        ])
        ->assertHasNoTableActionErrors();
});

it('can attach an existing record in tags relation manager', function (): void {
    $record = Post::factory()->create();
    $tag = Tag::factory()->create();

    livewire(PostResource\RelationManagers\TagsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->callTableAction('attach', null, [
            'recordId' => $tag->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect($record->refresh()->tags)->toHaveCount(1);
});

it('can detach a record in tags relation manager', function (): void {
    $record = Post::factory()
        ->has(Tag::factory())
        ->create();

    $tag = $record->tags->first();

    livewire(PostResource\RelationManagers\TagsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->callTableAction('detach', $tag)
        ->assertHasNoTableActionErrors();

    expect($record->refresh()->tags)->toBeEmpty();
});

it('renders the correct translated titles in tags relation manager', function (): void {
    $record = Post::factory()->create();

    livewire(PostResource\RelationManagers\TagsRelationManager::class, [
        'ownerRecord' => $record,
        'pageClass' => PostResource\Pages\EditPost::class,
    ])
        ->assertSee(__('admin/post-resource.relationships.tags'));
});
