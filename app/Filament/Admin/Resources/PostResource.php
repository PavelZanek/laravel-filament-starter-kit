<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\RelationManagers;
use App\Models\Content\Post;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Override;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('admin/post-resource.navigation_label');
    }

    #[Override]
    public static function getBreadcrumb(): string
    {
        return __('admin/post-resource.breadcrumb');
    }

    #[Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('admin/post-resource.attributes.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        fn (Forms\Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))
                    ),
                Forms\Components\TextInput::make('slug')
                    ->label(__('admin/post-resource.attributes.slug'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit'),

                Forms\Components\Builder::make('content')
                    ->label(__('common.blocks.content'))
                    ->addActionLabel(__('common.blocks.add_block'))
                    ->nullable()
                    ->blockNumbers(false)
                    ->reorderableWithButtons()
                    ->collapsed()
                    ->cloneable()
                    ->blockPickerColumns(3)
                    ->blocks([
                        Forms\Components\Builder\Block::make('heading')
                            ->label(__('common.blocks.heading'))
                            ->schema([
                                Forms\Components\TextInput::make('content')
                                    ->label(__('common.blocks.content'))
                                    ->required(),
                                Forms\Components\Select::make('level')
                                    ->label(__('common.blocks.level'))
                                    ->options([
                                        '2' => Str::ucfirst(__('common.blocks.level').' 2'),
                                        '3' => Str::ucfirst(__('common.blocks.level').' 3'),
                                        '4' => Str::ucfirst(__('common.blocks.level').' 4'),
                                        '5' => Str::ucfirst(__('common.blocks.level').' 5'),
                                        '6' => Str::ucfirst(__('common.blocks.level').' 6'),
                                    ])
                                    ->required(),
                            ])
                            ->columns(),
                        Forms\Components\Builder\Block::make('paragraph')
                            ->label(__('common.blocks.paragraph'))
                            ->schema([
                                Forms\Components\Textarea::make('content')
                                    ->label(__('common.blocks.content'))
                                    ->required(),
                            ]),
                        Forms\Components\Builder\Block::make('image')
                            ->label(__('common.blocks.image'))
                            ->schema([
                                Forms\Components\FileUpload::make('content')
                                    ->label(__('common.blocks.content'))
                                    ->image()
                                    ->required()
                                    ->openable(),
                            ]),
                    ]),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin/post-resource.attributes.name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('admin/post-resource.relationships.categories'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('published_at')
                    ->label(__('common.is_published'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('published_at'))
                    ->nullable()
                    ->placeholder(__('common.all'))
                    ->trueLabel(__('admin/post-resource.filters.published'))
                    ->falseLabel(__('admin/post-resource.filters.unpublished'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('published_at'),
                        false: fn (Builder $query): Builder => $query->whereNull('published_at'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
                Tables\Filters\SelectFilter::make('categories')
                    ->label(__('admin/post-resource.relationships.categories'))
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('tags')
                    ->label(__('admin/post-resource.relationships.tags'))
                    ->relationship('tags', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('common.created_from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('common.created_until'))
                            ->default(now()),
                    ])
                    // @codeCoverageIgnoreStart
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                function (Builder $query, mixed $date): Builder {
                                    /** @var ?string $date */
                                    return $query->whereDate('created_at', '>=', $date);
                                },
                            )
                            ->when(
                                $data['created_until'],
                                function (Builder $query, mixed $date): Builder {
                                    /** @var ?string $date */
                                    return $query->whereDate('created_at', '<=', $date);
                                },
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            // @phpstan-ignore-next-line
                            $indicators[] = Tables\Filters\Indicator::make(__('common.created_from').' '.Carbon::parse($data['created_from'])->translatedFormat(__('common.formats.date_string')))
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            // @phpstan-ignore-next-line
                            $indicators[] = Tables\Filters\Indicator::make(__('common.created_until').' '.Carbon::parse($data['created_until'])->translatedFormat(__('common.formats.date_string')))
                                ->removeField('created_until');
                        }

                        return $indicators;
                    }),
                // @codeCoverageIgnoreEnd
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button(),
                Tables\Actions\ViewAction::make()
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[Override]
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Split::make([
                    Infolists\Components\Grid::make()
                        ->schema([
                            Infolists\Components\Group::make([
                                Infolists\Components\TextEntry::make('name'),
                                Infolists\Components\TextEntry::make('slug'),
                                Infolists\Components\TextEntry::make('published_at')
                                    ->badge()
                                    ->date()
                                    ->color('success'),
                            ]),
                            Infolists\Components\Group::make([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->date(),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->date(),
                            ]),
                        ]),
                ]),

            ]);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            RelationManagers\AuthorsRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\TagsRelationManager::class,
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<Post>
     */
    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
