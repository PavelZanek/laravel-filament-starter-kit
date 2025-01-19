<?php

declare(strict_types=1);

use App\Filament\Exports\UserExporter;
use Filament\Actions\Exports\Models\Export;

function createExportStub(int $successful, int $failed): Export
{
    return new class($successful, $failed) extends Export
    {
        public int $successful_rows;

        protected int $failed;

        public function __construct(int $successful, int $failed)
        {
            $this->successful_rows = $successful;
            $this->failed = $failed;
        }

        public function getFailedRowsCount(): int
        {
            return $this->failed;
        }
    };
}

it('returns proper columns', function (): void {
    $columns = UserExporter::getColumns();

    expect($columns)->toBeArray()
        ->and(count($columns))->toBe(8);

    $expectedNames = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'roles.name',
        'created_at',
        'updated_at',
        'deleted_at',
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
