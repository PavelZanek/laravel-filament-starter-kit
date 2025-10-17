<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

final class BaseExporter
{
    /**
     * Get the base columns that are common across all exporters
     *
     * @return array<ExportColumn>
     */
    public static function getBaseColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('common.id')),
            ExportColumn::make('created_at')
                ->label(__('common.created_at')),
            ExportColumn::make('updated_at')
                ->label(__('common.updated_at')),
            ExportColumn::make('deleted_at')
                ->label(__('common.deleted_at')),
        ];
    }

    /**
     * Merge resource columns with base columns in the correct order
     *
     * @param  array<ExportColumn>  $resourceColumns
     * @return array<ExportColumn>
     */
    public static function mergeColumns(array $resourceColumns): array
    {
        $baseColumns = self::getBaseColumns();
        $columns = [];

        // Add ID first if not present in resource columns
        $idColumn = collect($baseColumns)->first(fn (ExportColumn $column): bool => $column->getName() === 'id');
        if ($idColumn && ! collect($resourceColumns)->contains(fn (ExportColumn $column): bool => $column->getName() === 'id')) {
            $columns[] = $idColumn;
        }

        // Add resource-specific columns
        $columns = array_merge($columns, $resourceColumns);

        // Add remaining base columns (created_at, updated_at, deleted_at) at the end
        $remainingBaseColumns = collect($baseColumns)
            ->filter(fn (ExportColumn $column): bool => $column->getName() !== 'id')
            ->filter(fn (ExportColumn $column): bool => ! collect($resourceColumns)->contains(fn (ExportColumn $resourceColumn): bool => $resourceColumn->getName() === $column->getName()))
            ->values()
            ->all();

        /** @var array<ExportColumn> $result */
        $result = array_merge($columns, $remainingBaseColumns);

        return $result;
    }

    /**
     * Generate a completed notification body with consistent messaging
     */
    public static function getCompletedNotificationBody(Export $export, string $entityName): string
    {
        $body = "Your {$entityName} export has completed and ".number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if (($failedRowsCount = $export->getFailedRowsCount()) !== 0) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
