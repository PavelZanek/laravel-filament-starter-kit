<?php

declare(strict_types=1);

namespace App\Helpers;

final readonly class ProjectHelper
{
    public static function getRecordsPerPageDefaultOption(): int
    {
        /** @var int $configValue */
        $configValue = config('project.records_per_page.default');

        return $configValue;
    }

    /**
     * @return int[]
     */
    public static function getRecordsPerPageOptions(): array
    {
        /** @var int[] $configValue */
        $configValue = config('project.records_per_page.options');

        return $configValue;
    }
}
