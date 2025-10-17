<?php

declare(strict_types=1);

namespace App\Filament\Traits;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

/**
 * Trait for common table columns across Filament resources.
 * Eliminates DRY violations by providing reusable column methods.
 */
trait CommonTableColumns
{
    /**
     * Get common audit columns (created_at, created_by, updated_at, updated_by, deleted_at, deleted_by).
     *
     * @return array<Tables\Columns\Column>
     */
    public static function getAuditTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label(__('common.created_at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('createdBy.name')
                ->label(__('common.created_by'))
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->label(__('common.updated_at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updatedBy.name')
                ->label(__('common.updated_by'))
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('deleted_at')
                ->label(__('common.deleted_at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('deletedBy.name')
                ->label(__('common.deleted_by'))
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
