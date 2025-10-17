<?php

declare(strict_types=1);

use App\Filament\Exports\Admin\UserExporter;

require_once __DIR__.'/../Helpers/ExportTestHelpers.php';

it('returns proper columns', function (): void {
    $columns = UserExporter::getColumns();

    expect($columns)->toBeArray()
        ->and(count($columns))->toBe(8);

    $expectedNames = [
        'id',                   // Base column (first)
        'name',                 // Resource column
        'email',                // Resource column
        'email_verified_at',    // Resource column
        'roles.name',           // Resource column
        'created_at',           // Base column (end)
        'updated_at',           // Base column (end)
        'deleted_at',           // Base column (end)
    ];

    foreach ($columns as $index => $column) {
        expect($column->getName())->toBe($expectedNames[$index]);
    }
});

it('returns correct completed notification body with no failures', function (): void {
    $export = createExportStub(100, 0);
    $body = UserExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('Your user export has completed')
        ->and($body)->toContain('100 rows exported')
        ->and($body)->not->toContain('failed to export');
});

it('returns correct completed notification body with failures', function (): void {
    $export = createExportStub(150, 5);
    $body = UserExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('Your user export has completed')
        ->and($body)->toContain('150 rows exported')
        ->and($body)->toContain('5 rows failed to export');
});
