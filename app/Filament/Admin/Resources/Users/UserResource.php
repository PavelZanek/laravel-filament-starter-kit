<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users;

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Exports\UserExporter;
use App\Helpers\ProjectHelper;
use App\Models\Role;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Override;

final class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = -2;

    #[Override]
    public static function getNavigationLabel(): string
    {
        return __('admin/user-resource.navigation_label');
    }

    #[Override] // @phpstan-ignore-line
    public static function getNavigationGroup(): ?string
    {
        return __('admin/user-resource.navigation_group');
    }

    public static function getNavigationBadge(): string
    {
        return strval(self::getEloquentQuery()->whereNull('deleted_at')->count());
    }

    #[Override]
    public static function getBreadcrumb(): string
    {
        return __('admin/user-resource.breadcrumb');
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('admin/user-resource.attributes.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('admin/user-resource.attributes.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label(__('admin/user-resource.attributes.password'))
                    ->required()
                    ->password()
                    ->maxLength(255)
                    ->rule(Password::default())
                    ->visibleOn('create'),
                Select::make('roles')
                    ->label(__('admin/user-resource.custom_attributes.role'))
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query): Builder {
                            $superAdminRole = Role::query()
                                ->where('name', Role::SUPER_ADMIN)
                                ->where('guard_name', Role::GUARD_NAME_WEB)
                                ->firstOrFail();

                            return $query->whereKeyNot($superAdminRole->getKey());
                        }
                    )
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->required()
                    ->minItems(1)
                    ->maxItems(1)
                    ->hidden(fn (?User $record): bool => $record?->hasRole(Role::SUPER_ADMIN) ?? false),
            ]);
    }

    /**
     * @throws Exception
     */
    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): void {
                $query->with('roles');
            })
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin/user-resource.attributes.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('admin/user-resource.attributes.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label(__('admin/user-resource.custom_attributes.role'))
                    ->badge()
                    // @phpstan-ignore-next-line
                    ->hidden(fn (HasTable $livewire): bool => $livewire->activeTab !== 'all')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('deleted_at')
                    ->label(__('common.is_active'))
                    ->state(function (User $record): bool {
                        // @codeCoverageIgnoreStart
                        return (bool) $record->deleted_at;
                        // @codeCoverageIgnoreEnd
                    })
                    ->icon(fn (string $state): string => $state === '' || $state === '0' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (string $state): string => $state === '' || $state === '0' ? 'success' : 'danger')
                    ->boolean()
                    ->visible(fn (HasTable $livewire): bool => isset($livewire->getTableFilterState('trashed')['value']) &&
                        $livewire->getTableFilterState('trashed')['value'] === '1'
                    ),
                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->label(__('common.is_verified'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->nullable()
                    ->placeholder(__('admin/User-resource.filters.all'))
                    ->trueLabel(__('admin/user-resource.filters.verified'))
                    ->falseLabel(__('admin/user-resource.filters.unverified'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query): Builder => $query->whereNull('email_verified_at'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
                TrashedFilter::make(),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label(__('common.created_from')),
                        DatePicker::make('created_until')
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
                            $indicators[] = Indicator::make(__('common.created_from').' '.Carbon::parse($data['created_from'])->translatedFormat(__('common.formats.date_string')))
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            // @phpstan-ignore-next-line
                            $indicators[] = Indicator::make(__('common.created_until').' '.Carbon::parse($data['created_until'])->translatedFormat(__('common.formats.date_string')))
                                ->removeField('created_until');
                        }

                        return $indicators;
                    }),
                // @codeCoverageIgnoreEnd
            ])
            ->recordActions([
                EditAction::make()
                    ->button(),
                Action::make('changePassword')
                    ->label(__('admin/user-resource.actions.change_password'))
                    ->action(function (User $record, array $data): void {
                        /** @var string $psw */
                        $psw = $data['new_password'];
                        $record->update(['password' => Hash::make($psw)]);

                        Notification::make()
                            ->success()
                            ->title(__('admin/user-resource.flash.password_changed'))
                            ->send();
                    })
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('new_password')
                                    ->label(__('admin/user-resource.custom_attributes.new_password'))
                                    ->password()
                                    ->required()
                                    ->rule(Password::default()),
                                TextInput::make('new_password_confirmation')
                                    ->label(__('admin/user-resource.custom_attributes.new_password_confirmation'))
                                    ->password()
                                    ->required()
                                    ->rule('required', fn (Get $get): bool => (bool) $get('new_password'))
                                    ->same('new_password'),
                            ]),
                    ])
                    ->modalSubmitActionLabel(__('admin/user-resource.actions.change_password'))
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->button()
                    ->icon('heroicon-o-key')
                    ->visible(fn (User $record): bool => ! $record->hasRole(Role::SUPER_ADMIN)),

                Action::make('changeRole')
                    ->label(__('admin/user-resource.actions.change_role'))
                    ->action(function (User $record, array $data): void {
                        /** @var string $role */
                        $role = $data['new_role'];
                        $record->roles()->sync([$role]);

                        Notification::make()
                            ->success()
                            ->title(__('admin/user-resource.flash.role_changed'))
                            ->send();
                    })
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('new_role')
                                    ->label(__('admin/user-resource.custom_attributes.role'))
                                    ->relationship(
                                        name: 'roles',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: function (Builder $query): Builder {
                                            $superAdminRole = Role::query()
                                                ->where('name', Role::SUPER_ADMIN)
                                                ->where('guard_name', Role::GUARD_NAME_WEB)
                                                ->firstOrFail();

                                            return $query->whereKeyNot($superAdminRole->getKey());
                                        }
                                    )
                                    ->preload()
                                    ->searchable()
                                    ->native(false)
                                    ->required()
                                    ->hidden(fn (?User $record): bool => $record?->hasRole(Role::SUPER_ADMIN) ?? false),
                            ]),
                    ])
                    ->modalSubmitActionLabel(__('admin/user-resource.actions.change_role'))
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->button()
                    ->icon('heroicon-o-shield-check')
                    ->visible(fn (User $record): bool => ! $record->hasRole(Role::SUPER_ADMIN)),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('common.export'))
                    ->modalHeading(__('admin/user-resource.list.export.modal_heading'))
                    ->exporter(UserExporter::class)
                    // @phpstan-ignore-next-line
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query->withTrashed()->with('roles'))
                    ->formats([
                        ExportFormat::Xlsx,
                        ExportFormat::Csv,
                    ]),
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('name')
            ->paginated(ProjectHelper::getRecordsPerPageOptions())
            ->defaultPaginationPageOption(ProjectHelper::getRecordsPerPageDefaultOption());
    }

    /**
     * @return Builder<User>
     */
    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
