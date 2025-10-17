<?php

declare(strict_types=1);

use Filament\Actions\Exports\Models\Export;

if (! function_exists('createExportStub')) {
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
}
