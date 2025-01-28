<?php

declare(strict_types=1);

use App\Helpers\ProjectHelper;

it('returns the default records per page value', function (): void {
    $defaultRecordsPerPage = 15;
    config(['project.records_per_page.default' => $defaultRecordsPerPage]);

    $result = ProjectHelper::getRecordsPerPageDefaultOption();

    expect($result)->toBe($defaultRecordsPerPage);
});

it('returns the records per page options', function (): void {
    $recordsPerPageOptions = [10, 15, 25, 50];
    config(['project.records_per_page.options' => $recordsPerPageOptions]);

    $result = ProjectHelper::getRecordsPerPageOptions();

    expect($result)->toBe($recordsPerPageOptions);
});
