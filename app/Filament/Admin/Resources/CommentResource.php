<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CommentResource\Pages;
use App\Models\Content\Comment;
use App\Models\Content\Post;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('admin/comment-resource.navigation_label');
    }

    #[Override] // @phpstan-ignore-line
    public static function getNavigationGroup(): ?string
    {
        return __('admin/comment-resource.navigation_group');
    }

    #[Override]
    public static function getBreadcrumb(): string
    {
        return __('admin/comment-resource.breadcrumb');
    }

    #[Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label(__('admin/comment-resource.attributes.user_id'))
                    ->relationship('user', 'email')
                    ->preload()
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('content')
                    ->label(__('admin/comment-resource.attributes.content'))
                    ->required()
                    ->maxLength(1000),
                Forms\Components\MorphToSelect::make('commentable')
                    ->label(__('admin/comment-resource.custom_attributes.commentable'))
                    ->types([
                        Forms\Components\MorphToSelect\Type::make(Post::class)
                            ->titleAttribute('name'),
                        //                        Forms\Components\MorphToSelect\Type::make(User::class)
                        //                            ->titleAttribute('email'),
                    ])
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): void {
                $query->with('commentable');
            })
            ->columns([
                Tables\Columns\TextColumn::make('commentable_id')
                    ->label(__('admin/comment-resource.attributes.commentable_id'))
                    ->formatStateUsing(function (Comment $record): string {
                        return match ($record->commentable_type) {
                            Post::class => $record->commentable instanceof Post ? $record->commentable->name : '',
                            default => '',
                        };
                    }),
                // badge
                Tables\Columns\TextColumn::make('commentable_type')
                    ->label(__('admin/comment-resource.attributes.commentable_type'))
                    ->formatStateUsing(function (Comment $record): string {
                        return match ($record->commentable_type) {
                            Post::class => 'post',
                            default => '',
                        };
                    })
                    ->badge(function (Comment $record): string {
                        return match ($record->commentable_type) {
                            Post::class => 'success',
                            default => 'secondary',
                        };
                    }),
                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('admin/comment-resource.attributes.user_id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('deleted_at')
                    ->label(__('common.is_active'))
                    ->state(function (Comment $record): bool {
                        // @codeCoverageIgnoreStart
                        return (bool) $record->deleted_at;
                        // @codeCoverageIgnoreEnd
                    })
                    ->icon(fn (string $state): string => $state === '' || $state === '0' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (string $state): string => $state === '' || $state === '0' ? 'success' : 'danger')
                    ->boolean()
                    ->visible(fn (Tables\Contracts\HasTable $livewire): bool => isset($livewire->getTableFilterState('trashed')['value']) &&
                        $livewire->getTableFilterState('trashed')['value'] === '1'
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
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
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->modalHeading(__('admin/comment-resource.actions.modals.delete.single.heading'))
                    ->modalDescription(__('admin/comment-resource.actions.modals.delete.single.description'))
                    ->successNotificationTitle(__('admin/comment-resource.flash.deleted')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading(__('admin/comment-resource.actions.modals.delete.bulk.heading'))
                        ->modalDescription(__('admin/comment-resource.actions.modals.delete.bulk.description'))
                        ->successNotificationTitle(__('admin/comment-resource.flash.deleted_bulk')),
                ]),
            ]);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<Comment>
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
