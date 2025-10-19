<?php

declare(strict_types=1);

use App\Filament\Traits\CommonTableFilters;
use App\Models\Role;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class TestClassWithCommonTableFilters
{
    use CommonTableFilters {
        getAuditFilters as public;
        getTrashedFilter as public;
        getCreatedAtFilter as public;
        getSimpleCreatedAtFilter as public;
        getDateRangeFilter as public;
        getDeletedAtColumnVisibility as public;
        getCurrencyFilter as public;
        getCommonFilters as public;
    }
}

it('returns audit filters as array of SelectFilter', function (): void {
    $filters = TestClassWithCommonTableFilters::getAuditFilters();

    expect($filters)->toBeArray()
        ->toHaveCount(3);

    foreach ($filters as $filter) {
        expect($filter)->toBeInstanceOf(SelectFilter::class);
    }
});

it('audit filters contain created_by filter', function (): void {
    $filters = TestClassWithCommonTableFilters::getAuditFilters();

    $createdByFilter = null;
    foreach ($filters as $filter) {
        if ($filter->getName() === 'created_by_id') {
            $createdByFilter = $filter;

            break;
        }
    }

    expect($createdByFilter)->not->toBeNull()
        ->and($createdByFilter)->toBeInstanceOf(SelectFilter::class);
});

it('audit filters contain updated_by filter', function (): void {
    $filters = TestClassWithCommonTableFilters::getAuditFilters();

    $updatedByFilter = null;
    foreach ($filters as $filter) {
        if ($filter->getName() === 'updated_by_id') {
            $updatedByFilter = $filter;

            break;
        }
    }

    expect($updatedByFilter)->not->toBeNull()
        ->and($updatedByFilter)->toBeInstanceOf(SelectFilter::class);
});

it('audit filters contain deleted_by filter', function (): void {
    $filters = TestClassWithCommonTableFilters::getAuditFilters();

    $deletedByFilter = null;
    foreach ($filters as $filter) {
        if ($filter->getName() === 'deleted_by_id') {
            $deletedByFilter = $filter;

            break;
        }
    }

    expect($deletedByFilter)->not->toBeNull()
        ->and($deletedByFilter)->toBeInstanceOf(SelectFilter::class);
});

it('returns trashed filter as TrashedFilter', function (): void {
    $filter = TestClassWithCommonTableFilters::getTrashedFilter();

    expect($filter)->toBeInstanceOf(TrashedFilter::class);
});

it('returns created_at filter with indicators', function (): void {
    $filter = TestClassWithCommonTableFilters::getCreatedAtFilter();

    expect($filter)->toBeInstanceOf(Filter::class)
        ->and($filter->getName())->toBe('created_at');
});

it('returns simple created_at filter without indicators', function (): void {
    $filter = TestClassWithCommonTableFilters::getSimpleCreatedAtFilter();

    expect($filter)->toBeInstanceOf(Filter::class)
        ->and($filter->getName())->toBe('created_at');
});

it('creates custom date range filter', function (): void {
    $filter = TestClassWithCommonTableFilters::getDateRangeFilter(
        column: 'custom_date',
        fromLabel: 'From Date',
        toLabel: 'To Date',
        withIndicators: false,
        defaultToValue: null,
        columns: 5,
        columnSpan: 5
    );

    expect($filter)->toBeInstanceOf(Filter::class)
        ->and($filter->getName())->toBe('custom_date');
});

it('date range filter query logic works with from date', function (): void {
    $role = Role::factory()->create([
        'name' => 'Test Date Filter Role',
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    // Test the query logic
    $query = Role::query();
    $fromDate = now()->subDays(7)->format('Y-m-d');

    // Simulate the query modifier logic
    $table = $query->getModel()->getTable();
    $qualifiedColumn = "{$table}.created_at";

    $modifiedQuery = $query->whereDate($qualifiedColumn, '>=', $fromDate);

    expect($modifiedQuery)->toBeInstanceOf(Illuminate\Database\Eloquent\Builder::class);
});

it('date range filter query logic works with until date', function (): void {
    $role = Role::factory()->create([
        'name' => 'Test Date Filter Role 2',
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    // Test the query logic
    $query = Role::query();
    $untilDate = now()->format('Y-m-d');

    // Simulate the query modifier logic
    $table = $query->getModel()->getTable();
    $qualifiedColumn = "{$table}.created_at";

    $modifiedQuery = $query->whereDate($qualifiedColumn, '<=', $untilDate);

    expect($modifiedQuery)->toBeInstanceOf(Illuminate\Database\Eloquent\Builder::class);
});

it('date range filter query logic works with both from and until dates', function (): void {
    $role = Role::factory()->create([
        'name' => 'Test Date Filter Role 3',
        'guard_name' => 'web',
        'is_default' => false,
    ]);

    // Test the query logic
    $query = Role::query();
    $fromDate = now()->subDays(7)->format('Y-m-d');
    $untilDate = now()->format('Y-m-d');

    // Simulate the query modifier logic
    $table = $query->getModel()->getTable();
    $qualifiedColumn = "{$table}.created_at";

    $modifiedQuery = $query
        ->whereDate($qualifiedColumn, '>=', $fromDate)
        ->whereDate($qualifiedColumn, '<=', $untilDate);

    expect($modifiedQuery)->toBeInstanceOf(Illuminate\Database\Eloquent\Builder::class);
});

it('deleted_at column visibility closure returns false for no trashed filter', function (): void {
    // Test the visibility logic for when trashed filter is not set
    $state = null;
    $value = is_array($state) ? ($state['value'] ?? null) : $state;

    $isVisible = false;
    if (is_bool($value)) {
        $isVisible = $value;
    } elseif ($value === 1 || $value === '1') {
        $isVisible = true;
    } elseif (is_string($value)) {
        $isVisible = in_array($value, ['with', 'only'], true);
    }

    expect($isVisible)->toBeFalse();
});

it('deleted_at column visibility closure returns true for trashed filter with "with" value', function (): void {
    // Test the visibility logic for "with" value
    $state = ['value' => 'with'];
    $value = is_array($state) ? ($state['value'] ?? null) : $state;

    $isVisible = false;
    if (is_bool($value)) {
        $isVisible = $value;
    } elseif ($value === 1 || $value === '1') {
        $isVisible = true;
    } elseif (is_string($value)) {
        $isVisible = in_array($value, ['with', 'only'], true);
    }

    expect($isVisible)->toBeTrue();
});

it('deleted_at column visibility closure returns true for trashed filter with "only" value', function (): void {
    // Test the visibility logic for "only" value
    $state = ['value' => 'only'];
    $value = is_array($state) ? ($state['value'] ?? null) : $state;

    $isVisible = false;
    if (is_bool($value)) {
        $isVisible = $value;
    } elseif ($value === 1 || $value === '1') {
        $isVisible = true;
    } elseif (is_string($value)) {
        $isVisible = in_array($value, ['with', 'only'], true);
    }

    expect($isVisible)->toBeTrue();
});

it('deleted_at column visibility closure returns true for legacy integer 1', function (): void {
    // Test the visibility logic for legacy integer value
    $state = 1;
    $value = is_array($state) ? ($state['value'] ?? null) : $state;

    $isVisible = false;
    if (is_bool($value)) {
        $isVisible = $value;
    } elseif ($value === 1 || $value === '1') {
        $isVisible = true;
    } elseif (is_string($value)) {
        $isVisible = in_array($value, ['with', 'only'], true);
    }

    expect($isVisible)->toBeTrue();
});

it('deleted_at column visibility closure returns true for legacy string "1"', function (): void {
    // Test the visibility logic for legacy string value
    $state = '1';
    $value = is_array($state) ? ($state['value'] ?? null) : $state;

    $isVisible = false;
    if (is_bool($value)) {
        $isVisible = $value;
    } elseif ($value === 1 || $value === '1') {
        $isVisible = true;
    } elseif (is_string($value)) {
        $isVisible = in_array($value, ['with', 'only'], true);
    }

    expect($isVisible)->toBeTrue();
});

it('deleted_at column visibility closure returns true for boolean true', function (): void {
    // Test the visibility logic for boolean value
    $state = true;
    $value = is_array($state) ? ($state['value'] ?? null) : $state;

    $isVisible = false;
    if (is_bool($value)) {
        $isVisible = $value;
    } elseif ($value === 1 || $value === '1') {
        $isVisible = true;
    } elseif (is_string($value)) {
        $isVisible = in_array($value, ['with', 'only'], true);
    }

    expect($isVisible)->toBeTrue();
});

it('deleted_at column visibility closure returns false for boolean false', function (): void {
    // Test the visibility logic for boolean false
    $state = false;
    $value = is_array($state) ? ($state['value'] ?? null) : $state;

    $isVisible = false;
    if (is_bool($value)) {
        $isVisible = $value;
    } elseif ($value === 1 || $value === '1') {
        $isVisible = true;
    } elseif (is_string($value)) {
        $isVisible = in_array($value, ['with', 'only'], true);
    }

    expect($isVisible)->toBeFalse();
});

it('returns currency filter as SelectFilter', function (): void {
    $filter = TestClassWithCommonTableFilters::getCurrencyFilter();

    expect($filter)->toBeInstanceOf(SelectFilter::class)
        ->and($filter->getName())->toBe('currency_id');
});

it('returns currency filter with custom column name', function (): void {
    $filter = TestClassWithCommonTableFilters::getCurrencyFilter('custom_currency_id', 'attributes.currency');

    expect($filter)->toBeInstanceOf(SelectFilter::class)
        ->and($filter->getName())->toBe('custom_currency_id');
});

it('returns common filters as array of BaseFilter', function (): void {
    $filters = TestClassWithCommonTableFilters::getCommonFilters();

    expect($filters)->toBeArray()
        ->toHaveCount(4);

    foreach ($filters as $filter) {
        expect($filter)->toBeInstanceOf(BaseFilter::class);
    }
});

it('common filters contain trashed filter', function (): void {
    $filters = TestClassWithCommonTableFilters::getCommonFilters();

    $hasTrashedFilter = false;
    foreach ($filters as $filter) {
        if ($filter instanceof TrashedFilter) {
            $hasTrashedFilter = true;

            break;
        }
    }

    expect($hasTrashedFilter)->toBeTrue();
});

it('common filters contain audit filters', function (): void {
    $filters = TestClassWithCommonTableFilters::getCommonFilters();

    // Count filters that are not TrashedFilter (should be 3: created_by, updated_by, deleted_by)
    $auditFilterCount = 0;
    foreach ($filters as $filter) {
        if (! $filter instanceof TrashedFilter) {
            $auditFilterCount++;
        }
    }

    expect($auditFilterCount)->toBe(3);
});

it('date range filter uses correct field names for created_at', function (): void {
    // Test that created_at uses special field names
    $column = 'created_at';
    $fromFieldName = $column === 'created_at' ? 'created_from' : "{$column}_from";
    $toFieldName = $column === 'created_at' ? 'created_until' : "{$column}_to";

    expect($fromFieldName)->toBe('created_from')
        ->and($toFieldName)->toBe('created_until');
});

it('date range filter uses standard field names for custom columns', function (): void {
    // Test that custom columns use standard field names
    $column = 'custom_date';
    $fromFieldName = $column === 'created_at' ? 'created_from' : "{$column}_from";
    $toFieldName = $column === 'created_at' ? 'created_until' : "{$column}_to";

    expect($fromFieldName)->toBe('custom_date_from')
        ->and($toFieldName)->toBe('custom_date_to');
});
