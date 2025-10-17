<?php

declare(strict_types=1);

namespace App\Filament\Traits;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for common table filters across Filament resources.
 * Eliminates DRY violations by providing reusable filter methods.
 */
trait CommonTableFilters
{
    /**
     * Get common audit filters (created_by, updated_by, deleted_by).
     *
     * @return array<SelectFilter>
     */
    public static function getAuditFilters(): array
    {
        return [
            SelectFilter::make('created_by_id')
                ->label(__('common.created_by'))
                ->relationship('createdBy', 'name'),
            SelectFilter::make('updated_by_id')
                ->label(__('common.updated_by'))
                ->relationship('updatedBy', 'name'),
            SelectFilter::make('deleted_by_id')
                ->label(__('common.deleted_by'))
                ->relationship('deletedBy', 'name'),
        ];
    }

    /**
     * Get trashed filter.
     */
    public static function getTrashedFilter(): TrashedFilter
    {
        return TrashedFilter::make();
    }

    /**
     * Get date range filter for created_at with indicators.
     */
    public static function getCreatedAtFilter(): Filter
    {
        return static::getDateRangeFilter(
            column: 'created_at',
            fromLabel: __('common.created_from'),
            toLabel: __('common.created_until'),
            withIndicators: true,
            defaultToValue: now(),
            columns: 2,
            columnSpan: 2
        );
    }

    /**
     * Get simple date range filter for created_at without indicators.
     */
    public static function getSimpleCreatedAtFilter(): Filter
    {
        return static::getDateRangeFilter(
            column: 'created_at',
            fromLabel: __('common.created_from'),
            toLabel: __('common.created_until'),
            withIndicators: false,
            defaultToValue: null,
            columns: 5,
            columnSpan: 5
        );
    }

    /**
     * Get date range filter for any date column.
     *
     * @param  string  $column  The date column name (e.g., 'date', 'start_date')
     * @param  string  $fromLabel  Label for "from" field
     * @param  string  $toLabel  Label for "to" field
     * @param  bool  $withIndicators  Whether to show filter indicators
     * @param  mixed  $defaultToValue  Default value for "to" field
     * @param  int  $columns  Number of columns for layout
     * @param  int  $columnSpan  Column span for the filter
     */
    public static function getDateRangeFilter(
        string $column,
        string $fromLabel,
        string $toLabel,
        bool $withIndicators = false,
        mixed $defaultToValue = null,
        int $columns = 5,
        int $columnSpan = 5
    ): Filter {
        $fromFieldName = $column === 'created_at' ? 'created_from' : "{$column}_from";
        $toFieldName = $column === 'created_at' ? 'created_until' : "{$column}_to";

        $filter = Filter::make($column)
            ->schema([
                DatePicker::make($fromFieldName)
                    ->label($fromLabel),
                DatePicker::make($toFieldName)
                    ->label($toLabel)
                    ->when($defaultToValue !== null, fn (DatePicker $component): DatePicker => $component->default($defaultToValue)),
            ])
            ->columns($columns === 2 ? null : $columns)
            ->when($columns === 2, fn (Filter $filter): Filter => $filter->columns())
            ->columnSpan($columnSpan)
            ->query(function (Builder $query, array $data) use ($column, $fromFieldName, $toFieldName): Builder {
                /** @var string|null $from */
                $from = $data[$fromFieldName] ?? null;

                /** @var string|null $until */
                $until = $data[$toFieldName] ?? null;

                return $query
                    ->when($from, fn (Builder $query) => $query->whereDate($column, '>=', $from))
                    ->when($until, fn (Builder $query) => $query->whereDate($column, '<=', $until));
            });

        if ($withIndicators) {
            return $filter->indicateUsing(function (array $data) use ($fromFieldName, $toFieldName, $fromLabel, $toLabel): array {
                $indicators = [];

                if ($data[$fromFieldName] ?? null) {
                    // @phpstan-ignore-next-line
                    $indicators[] = Indicator::make($fromLabel.' '.Carbon::parse($data[$fromFieldName])->translatedFormat(__('common.formats.date_string')))
                        ->removeField($fromFieldName);
                }

                if ($data[$toFieldName] ?? null) {
                    // @phpstan-ignore-next-line
                    $indicators[] = Indicator::make($toLabel.' '.Carbon::parse($data[$toFieldName])->translatedFormat(__('common.formats.date_string')))
                        ->removeField($toFieldName);
                }

                return $indicators;
            });
        }

        return $filter;
    }

    /**
     * Get visibility closure for deleted_at column based on trashed filter state.
     *
     * Visible when the Trashed filter is set to "with" or "only" (Filament v4),
     * and also supports legacy truthy values ('1', 1, true).
     */
    public static function getDeletedAtColumnVisibility(): Closure
    {
        return static function (HasTable $livewire): bool {
            $state = $livewire->getTableFilterState('trashed');

            $value = is_array($state) ? ($state['value'] ?? null) : $state;

            if (is_bool($value)) {
                return $value;
            }

            if ($value === 1 || $value === '1') {
                return true;
            }

            if (is_string($value)) {
                return in_array($value, ['with', 'only'], true);
            }

            return false;
        };
    }

    /**
     * Get currency filter.
     *
     * @param  string  $column  The currency column name (default: 'currency_id')
     * @param  string  $translationKey  The translation key for the label
     */
    public static function getCurrencyFilter(string $column = 'currency_id', string $translationKey = 'attributes.currency_id'): SelectFilter
    {
        return SelectFilter::make($column)
            ->label(__($translationKey))
            ->relationship('currency', 'name')
            ->searchable();
    }

    /**
     * Get complete set of common filters (trashed + audit filters).
     *
     * @return array<BaseFilter>
     */
    public static function getCommonFilters(): array
    {
        return [
            static::getTrashedFilter(),
            ...static::getAuditFilters(),
        ];
    }
}
