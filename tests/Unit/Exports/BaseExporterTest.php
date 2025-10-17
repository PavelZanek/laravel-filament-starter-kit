<?php

declare(strict_types=1);

use App\Filament\Exports\BaseExporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

require_once __DIR__.'/Helpers/ExportTestHelpers.php';

// Create a concrete implementation for testing
class TestExporter extends Exporter
{
    protected static ?string $model = App\Models\User::class;

    public static function getColumns(): array
    {
        return BaseExporter::mergeColumns(self::getResourceColumns());
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return BaseExporter::getCompletedNotificationBody($export, 'test');
    }

    /**
     * @return array<ExportColumn>
     */
    private static function getResourceColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Name'),
            ExportColumn::make('email')
                ->label('Email'),
        ];
    }
}

it('merges resource columns with base columns correctly', function (): void {
    $columns = TestExporter::getColumns();

    expect($columns)->toBeArray()
        ->and(count($columns))->toBe(6); // id + 2 resource + 3 base (created_at, updated_at, deleted_at)

    $expectedNames = [
        'id',           // Base column (first)
        'name',         // Resource column
        'email',        // Resource column
        'created_at',   // Base column (end)
        'updated_at',   // Base column (end)
        'deleted_at',   // Base column (end)
    ];

    foreach ($columns as $index => $column) {
        expect($column->getName())->toBe($expectedNames[$index]);
    }
});

it('returns correct completed notification body with no failures', function (): void {
    $export = createExportStub(100, 0);
    $body = TestExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('Your test export has completed')
        ->and($body)->toContain('100 rows exported')
        ->and($body)->not->toContain('failed to export');
});

it('returns correct completed notification body with failures', function (): void {
    $export = createExportStub(150, 5);
    $body = TestExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('Your test export has completed')
        ->and($body)->toContain('150 rows exported')
        ->and($body)->toContain('5 rows failed to export');
});

it('handles single row exports correctly', function (): void {
    $export = createExportStub(1, 0);
    $body = TestExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('1 row exported')
        ->and($body)->not->toContain('rows exported'); // Should use singular
});

it('handles single row failure correctly', function (): void {
    $export = createExportStub(10, 1);
    $body = TestExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('10 rows exported')
        ->and($body)->toContain('1 row failed'); // Should use singular
});
