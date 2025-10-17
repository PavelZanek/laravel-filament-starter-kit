<?php

declare(strict_types=1);

namespace App\Filament\Traits;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

trait HasAuditTable
{
    /**
     * Apply audit query with eager loading for audit relationships.
     */
    public static function applyAuditQuery(Table $table): Table
    {
        return $table->modifyQueryUsing(
            fn (Builder $query): Builder => $query->with(['createdBy', 'updatedBy', 'deletedBy'])
        );
    }

    /**
     * Get standard record actions (Edit, Delete, Restore, ForceDelete).
     *
     * @return array<ActionGroup>
     */
    public static function standardRecordActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make()
                    ->visible(fn (Model $record): bool => method_exists($record, 'trashed') && $record->trashed() && Gate::allows('restore', $record)),
                ForceDeleteAction::make()
                    ->visible(fn (Model $record): bool => method_exists($record, 'trashed') && $record->trashed() && Gate::allows('forceDelete', $record)),
            ]),
        ];
    }

    /**
     * Get standard CRUD actions that can be mixed with custom actions.
     *
     * @return array<\Filament\Actions\Action>
     */
    public static function standardCrudActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
            RestoreAction::make()
                ->visible(fn (Model $record): bool => method_exists($record, 'trashed') && $record->trashed() && Gate::allows('restore', $record)),
            ForceDeleteAction::make()
                ->visible(fn (Model $record): bool => method_exists($record, 'trashed') && $record->trashed() && Gate::allows('forceDelete', $record)),
        ];
    }

    /**
     * Get standard toolbar actions (DeleteBulkAction).
     *
     * @return array<BulkActionGroup>
     */
    public static function standardToolbarActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ];
    }
}
