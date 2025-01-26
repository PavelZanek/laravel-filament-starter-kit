<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Models\Content\Category;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Override;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('admin/category-resource.navigation_label');
    }

    #[Override] // @phpstan-ignore-line
    public static function getNavigationGroup(): ?string
    {
        return __('admin/category-resource.navigation_group');
    }

    #[Override]
    public static function getBreadcrumb(): string
    {
        return __('admin/category-resource.breadcrumb');
    }

    #[Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('admin/category-resource.attributes.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        fn (Forms\Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))
                    ),
                Forms\Components\TextInput::make('slug')
                    ->label(__('admin/category-resource.attributes.slug'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit'),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin/category-resource.attributes.name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('deleted_at')
                    ->label(__('common.is_active'))
                    ->state(function (Category $record): bool {
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
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
                    ->button()
                    ->modalHeading(__('admin/category-resource.edit.title'))
                    ->successNotificationTitle(__('admin/category-resource.flash.updated')),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->modalHeading(__('admin/category-resource.actions.modals.delete.single.heading'))
                    ->modalDescription(__('admin/category-resource.actions.modals.delete.single.description'))
                    ->successNotificationTitle(__('admin/category-resource.flash.deleted')),
                Tables\Actions\ForceDeleteAction::make()
                    ->button()
                    ->modalHeading(__('admin/category-resource.actions.modals.force_delete.single.heading'))
                    ->modalDescription(__('admin/category-resource.actions.modals.force_delete.single.description'))
                    ->successNotificationTitle(__('admin/category-resource.flash.force_deleted')),
                Tables\Actions\RestoreAction::make()
                    ->button()
                    ->modalHeading(__('admin/category-resource.actions.modals.restore.single.heading'))
                    ->modalDescription(__('admin/category-resource.actions.modals.restore.single.description'))
                    ->successNotificationTitle(__('admin/category-resource.flash.restored')),
            ])
            ->bulkActions([
                //                Tables\Actions\BulkActionGroup::make([
                //                    Tables\Actions\DeleteBulkAction::make(),
                //                ]),
            ]);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCategories::route('/'),
        ];
    }

    /**
     * @return Builder<Category>
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
