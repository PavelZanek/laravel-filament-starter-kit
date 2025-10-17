<?php

declare(strict_types=1);

use App\Filament\Exports\Admin\RoleExporter;

require_once __DIR__.'/../Helpers/ExportTestHelpers.php';

it('returns proper columns', function (): void {
    $columns = RoleExporter::getColumns();

    expect($columns)->toBeArray()
        ->and(count($columns))->toBe(10); // 6 resource + 4 base columns

    $expectedNames = [
        'id',                   // Base column (first)
        'name',                 // Resource column
        'guard_name',           // Resource column
        'team.name',            // Resource column
        'permissions_count',    // Resource column
        'users_count',          // Resource column
        'is_default',           // Resource column
        'created_at',           // Base column (end)
        'updated_at',           // Base column (end)
        'deleted_at',           // Base column (end)
    ];

    foreach ($columns as $index => $column) {
        expect($column->getName())->toBe($expectedNames[$index]);
    }
});

it('returns correct completed notification body with no failures', function (): void {
    $export = createExportStub(25, 0);
    $body = RoleExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('Your role export has completed')
        ->and($body)->toContain('25 rows exported')
        ->and($body)->not->toContain('failed to export');
});

it('returns correct completed notification body with failures', function (): void {
    $export = createExportStub(20, 2);
    $body = RoleExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('Your role export has completed')
        ->and($body)->toContain('20 rows exported')
        ->and($body)->toContain('2 rows failed to export');
});
