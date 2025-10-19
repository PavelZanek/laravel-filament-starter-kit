<?php

declare(strict_types=1);

namespace App\Filament\Traits;

use Closure;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Exporter;
use Illuminate\Database\Eloquent\Builder;

trait HasExportAction
{
    /**
     * Create a standardized export action for tables
     *
     * @param  class-string<Exporter>  $exporterClass
     * @param  array<ExportFormat>  $formats
     */
    protected static function createExportAction(
        string $exporterClass,
        string $label,
        string $modalHeading,
        ?Closure $queryModifier = null,
        array $formats = [ExportFormat::Xlsx, ExportFormat::Csv]
    ): ExportAction {
        $action = ExportAction::make()
            ->label($label)
            ->modalHeading($modalHeading)
            ->exporter($exporterClass)
            ->formats($formats);

        if ($queryModifier instanceof Closure) {
            $action->modifyQueryUsing($queryModifier);
        }

        return $action;
    }

    /**
     * Create a standard export action with common query modifications
     *
     * @param  class-string<Exporter>  $exporterClass
     * @param  array<string>  $eagerLoadRelations
     */
    protected static function createStandardExportAction(
        string $exporterClass,
        string $resourceName,
        array $eagerLoadRelations = []
    ): ExportAction {
        return static::createExportAction(
            exporterClass: $exporterClass,
            label: __('common.export'),
            modalHeading: __("{$resourceName}.list.export.modal_heading"),
            queryModifier: fn (Builder $query): Builder => static::applyStandardQueryModifications($query, $eagerLoadRelations)
        );
    }

    /**
     * Apply standard query modifications for exports
     *
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  array<string>  $eagerLoadRelations
     * @return Builder<\Illuminate\Database\Eloquent\Model>
     */
    protected static function applyStandardQueryModifications(Builder $query, array $eagerLoadRelations = []): Builder
    {
        // @phpstan-ignore-next-line - withTrashed() is available on models with SoftDeletes
        $query = $query->withTrashed();

        if ($eagerLoadRelations !== []) {
            // @codeCoverageIgnoreStart
            // @phpstan-ignore-next-line - with() method is available on Builder
            return $query->with($eagerLoadRelations);
            // @codeCoverageIgnoreEnd
        }

        // @phpstan-ignore-next-line - return type is correct
        return $query;
    }
}
