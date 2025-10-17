<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Tables;

use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Filament\Exports\Admin\RoleExporter;
use App\Filament\Traits\HasAuditTable;
use App\Filament\Traits\HasExportAction;
use App\Models\Role;
use Exception;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class RoleTable
{
    use HasAuditTable;
    use HasExportAction;

    /**
     * @throws Exception
     */
    public static function configure(Table $table): Table
    {
        return self::applyAuditQuery($table)
            ->columns([
                TextColumn::make('name')
                    ->weight('font-medium')
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn (mixed $state): string => Str::headline(is_string($state) ? $state : ''))
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->badge()
                    ->color('warning')
                    ->label(__('filament-shield::filament-shield.column.guard_name')),
                TextColumn::make('team.name')
                    ->default('Global')
                    ->badge()
                    ->color(fn (mixed $state): string => str(is_string($state) ? $state : '')->contains('Global') ? 'gray' : 'primary')
                    ->label(__('filament-shield::filament-shield.column.team'))
                    ->searchable()
                    ->visible(fn (): bool => RoleResource::shield()->isCentralApp() && \BezhanSalleh\FilamentShield\Support\Utils::isTenancyEnabled()),
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
                IconColumn::make('is_deleted')
                    ->label(__('common.is_deleted'))
                    ->state(fn (Role $record): bool => $record->trashed())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->visible(RoleResource::getDeletedAtColumnVisibility()),
                ...RoleResource::getAuditTableColumns(),
            ])
            ->filters(
                RoleResource::getCommonFilters()
            )
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make()
                        ->visible(fn (Role $record): bool => auth()->user()?->can('delete', $record)
                            && ! $record->is_default
                        ),
                    RestoreAction::make()
                        ->visible(fn (Role $record): bool => $record->trashed() && ! $record->is_default && Gate::allows('restore', $record)),
                    ForceDeleteAction::make()
                        ->visible(fn (Role $record): bool => $record->trashed() && ! $record->is_default && Gate::allows('forceDelete', $record)),
                ]),
            ])
            ->headerActions([
                self::createStandardExportAction(
                    exporterClass: RoleExporter::class,
                    resourceName: 'admin/role-resource',
                    eagerLoadRelations: ['permissions', 'users']
                ),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
