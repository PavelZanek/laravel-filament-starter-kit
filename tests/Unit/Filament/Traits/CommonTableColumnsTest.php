<?php

declare(strict_types=1);

use App\Filament\Traits\CommonTableColumns;
use Filament\Tables\Columns\TextColumn;

class TestClassWithCommonTableColumns
{
    use CommonTableColumns {
        getAuditTableColumns as public;
    }
}

it('returns audit table columns as array', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    expect($columns)->toBeArray()
        ->toHaveCount(6);
});

it('all audit columns are TextColumn instances', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    foreach ($columns as $column) {
        expect($column)->toBeInstanceOf(TextColumn::class);
    }
});

it('audit columns contain created_at column', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $createdAtColumn = null;
    foreach ($columns as $column) {
        if ($column->getName() === 'created_at') {
            $createdAtColumn = $column;

            break;
        }
    }

    expect($createdAtColumn)->not->toBeNull()
        ->and($createdAtColumn)->toBeInstanceOf(TextColumn::class);
});

it('audit columns contain createdBy.name column', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $createdByColumn = null;
    foreach ($columns as $column) {
        if ($column->getName() === 'createdBy.name') {
            $createdByColumn = $column;

            break;
        }
    }

    expect($createdByColumn)->not->toBeNull()
        ->and($createdByColumn)->toBeInstanceOf(TextColumn::class);
});

it('audit columns contain updated_at column', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $updatedAtColumn = null;
    foreach ($columns as $column) {
        if ($column->getName() === 'updated_at') {
            $updatedAtColumn = $column;

            break;
        }
    }

    expect($updatedAtColumn)->not->toBeNull()
        ->and($updatedAtColumn)->toBeInstanceOf(TextColumn::class);
});

it('audit columns contain updatedBy.name column', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $updatedByColumn = null;
    foreach ($columns as $column) {
        if ($column->getName() === 'updatedBy.name') {
            $updatedByColumn = $column;

            break;
        }
    }

    expect($updatedByColumn)->not->toBeNull()
        ->and($updatedByColumn)->toBeInstanceOf(TextColumn::class);
});

it('audit columns contain deleted_at column', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $deletedAtColumn = null;
    foreach ($columns as $column) {
        if ($column->getName() === 'deleted_at') {
            $deletedAtColumn = $column;

            break;
        }
    }

    expect($deletedAtColumn)->not->toBeNull()
        ->and($deletedAtColumn)->toBeInstanceOf(TextColumn::class);
});

it('audit columns contain deletedBy.name column', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $deletedByColumn = null;
    foreach ($columns as $column) {
        if ($column->getName() === 'deletedBy.name') {
            $deletedByColumn = $column;

            break;
        }
    }

    expect($deletedByColumn)->not->toBeNull()
        ->and($deletedByColumn)->toBeInstanceOf(TextColumn::class);
});

it('created_at column is sortable', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $createdAtColumn = null;
    foreach ($columns as $column) {
        if ($column->getName() === 'created_at') {
            $createdAtColumn = $column;

            break;
        }
    }

    expect($createdAtColumn)->not->toBeNull();
    expect($createdAtColumn->isSortable())->toBeTrue();
});

it('updated_at column is sortable', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $updatedAtColumn = null;
    foreach ($columns as $column) {
        if ($column->getName() === 'updated_at') {
            $updatedAtColumn = $column;

            break;
        }
    }

    expect($updatedAtColumn)->not->toBeNull();
    expect($updatedAtColumn->isSortable())->toBeTrue();
});

it('deleted_at column is sortable', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $deletedAtColumn = null;
    foreach ($columns as $column) {
        if ($column->getName() === 'deleted_at') {
            $deletedAtColumn = $column;

            break;
        }
    }

    expect($deletedAtColumn)->not->toBeNull();
    expect($deletedAtColumn->isSortable())->toBeTrue();
});

it('all audit columns are toggleable', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    foreach ($columns as $column) {
        expect($column->isToggleable())->toBeTrue();
    }
});

it('all audit columns are toggled hidden by default', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    foreach ($columns as $column) {
        expect($column->isToggledHiddenByDefault())->toBeTrue();
    }
});

it('date columns have expected names', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $dateColumnNames = [];
    foreach ($columns as $column) {
        if (in_array($column->getName(), ['created_at', 'updated_at', 'deleted_at'])) {
            $dateColumnNames[] = $column->getName();
        }
    }

    expect($dateColumnNames)
        ->toHaveCount(3)
        ->toContain('created_at', 'updated_at', 'deleted_at');
});

it('relationship columns have expected names', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $relationshipColumnNames = [];
    foreach ($columns as $column) {
        if (in_array($column->getName(), ['createdBy.name', 'updatedBy.name', 'deletedBy.name'])) {
            $relationshipColumnNames[] = $column->getName();
        }
    }

    expect($relationshipColumnNames)
        ->toHaveCount(3)
        ->toContain('createdBy.name', 'updatedBy.name', 'deletedBy.name');
});

it('audit columns are returned in correct order', function (): void {
    $columns = TestClassWithCommonTableColumns::getAuditTableColumns();

    $expectedOrder = [
        'created_at',
        'createdBy.name',
        'updated_at',
        'updatedBy.name',
        'deleted_at',
        'deletedBy.name',
    ];

    $actualOrder = array_map(fn (Filament\Tables\Columns\Column $column): string => $column->getName(), $columns);

    expect($actualOrder)->toBe($expectedOrder);
});
