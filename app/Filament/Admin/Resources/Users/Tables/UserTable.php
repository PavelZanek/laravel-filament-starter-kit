<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Exports\Admin\UserExporter;
use App\Filament\Traits\HasAuditTable;
use App\Filament\Traits\HasExportAction;
use App\Models\Role;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

final class UserTable
{
    use HasAuditTable;
    use HasExportAction;

    /**
     * @throws Exception
     */
    public static function configure(Table $table): Table
    {
        return self::applyAuditQuery($table)
            ->modifyQueryUsing(function (Builder $query): void {
                $query->with(['roles']);
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
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_deleted')
                    ->label(__('common.is_deleted'))
                    ->state(fn (User $record): bool => $record->trashed())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->visible(UserResource::getDeletedAtColumnVisibility()),
                ...UserResource::getAuditTableColumns(),
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
                // @codeCoverageIgnoreStart
                UserResource::getCreatedAtFilter(),
                // @codeCoverageIgnoreEnd
                ...UserResource::getCommonFilters(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
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
                                ->columnSpanFull()
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
                                ->columnSpanFull()
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
                        ->icon('heroicon-o-shield-check')
                        ->visible(fn (User $record): bool => ! $record->hasRole(Role::SUPER_ADMIN)),

                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->headerActions([
                self::createStandardExportAction(
                    exporterClass: UserExporter::class,
                    resourceName: 'admin/user-resource',
                    eagerLoadRelations: ['roles']
                ),
            ])
            ->defaultSort('name');
    }
}
