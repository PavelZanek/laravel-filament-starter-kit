<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages\CreateRole;
use App\Filament\Admin\Resources\RoleResource\Pages\EditRole;
use App\Filament\Admin\Resources\RoleResource\Pages\ListRoles;
use App\Filament\Admin\Resources\RoleResource\Pages\ViewRole;
use App\Helpers\ProjectHelper;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Override;

final class RoleResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * @return string[]
     */
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255),

                                Select::make(config('permission.column_names.team_foreign_key')) // @phpstan-ignore-line
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    /** @phpstan-ignore-next-line */
                                    ->default(Filament::getTenant()?->id)
                                    // ->options(fn (): Arrayable => Utils::getTenantModel() ? Utils::getTenantModel()::pluck('name', 'id') : collect())
                                    ->options(fn (): Arrayable => in_array(Utils::getTenantModel(), [null, '', '0'], true) ? collect() : Utils::getTenantModel()::pluck('name', 'id')) // @phpstan-ignore-line
                                    ->hidden(fn (): bool => ! (self::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                                    ->dehydrated(fn (): bool => ! (self::shield()->isCentralApp() && Utils::isTenancyEnabled())),
                                self::getSelectAllFormComponent(),

                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                self::getShieldFormComponents(),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight('font-medium')
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn (mixed $state): string => Str::headline($state)) // @phpstan-ignore-line
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->badge()
                    ->color('warning')
                    ->label(__('filament-shield::filament-shield.column.guard_name')),
                TextColumn::make('team.name')
                    ->default('Global')
                    ->badge()
                    ->color(fn (mixed $state): string => str($state)->contains('Global') ? 'gray' : 'primary') // @phpstan-ignore-line
                    ->label(__('filament-shield::filament-shield.column.team'))
                    ->searchable()
                    ->visible(fn (): bool => self::shield()->isCentralApp() && Utils::isTenancyEnabled()),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'gray',
                        default => 'success',
                    }),
                TextColumn::make('users_count')
                    ->badge()
                    ->label(__('admin/role-resource.custom_attributes.users_count'))
                    ->counts('users')
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'gray',
                        default => 'success',
                    }),
                IconColumn::make('is_default')
                    ->label(__('common.is_default'))
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->button(),
                DeleteAction::make()
                    ->button()
                    ->visible(fn (Role $record): bool => auth()->user()?->can('delete', $record)
                        && ! $record->is_default
                    ),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ])
            ->paginated(ProjectHelper::getRecordsPerPageOptions())
            ->defaultPaginationPageOption(ProjectHelper::getRecordsPerPageDefaultOption());
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster() ?? self::$cluster; // @phpstan-ignore-line
    }

    /**
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    #[Override]
    public static function getModel(): string
    {
        return Utils::getRoleModel(); // @phpstan-ignore-line
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.role');
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.roles');
    }

    #[Override]
    public static function shouldRegisterNavigation(): bool
    {
        return (bool) config('filament-shield.shield_resource.should_register_navigation', true);
    }

    #[Override]
    public static function getNavigationGroup(): string
    {
        return config('filament-shield.shield_resource.navigation_group', true)
            ? __('filament-shield::filament-shield.nav.group')
            : '';
    }

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('filament-shield::filament-shield.nav.role.label');
    }

    #[Override]
    public static function getNavigationIcon(): string
    {
        return __('filament-shield::filament-shield.nav.role.icon');
    }

    #[Override]
    public static function getNavigationSort(): ?int
    {
        /** @var int|null $sort */
        $sort = config('filament-shield.shield_resource.navigation_sort', -1);

        return $sort;
    }

    #[Override]
    public static function getSlug(?Panel $panel = null): string
    {
        return Utils::getResourceSlug();
    }

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return Utils::isResourceNavigationBadgeEnabled()
    //            ? strval(self::getEloquentQuery()->count())
    //            : null;
    //    }

    #[Override]
    public static function isScopedToTenant(): bool
    {
        return (bool) config('filament-shield.shield_resource.is_scoped_to_tenant', true);
    }

    #[Override]
    public static function canGloballySearch(): bool
    {
        return (bool) config('filament-shield.shield_resource.is_globally_searchable', false) && count(self::getGloballySearchableAttributes()) && self::canViewAny();
    }
}
