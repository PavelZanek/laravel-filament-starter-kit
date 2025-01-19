<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('common.id')),
            ExportColumn::make('name')
                ->label(__('admin/user-resource.attributes.name')),
            ExportColumn::make('email')
                ->label(__('admin/user-resource.attributes.email')),
            ExportColumn::make('email_verified_at')
                ->label(__('admin/user-resource.attributes.email_verified_at')),
            ExportColumn::make('roles.name')
                ->label(__('admin/user-resource.custom_attributes.role')),
            ExportColumn::make('created_at')
                ->label(__('common.created_at')),
            ExportColumn::make('updated_at')
                ->label(__('common.updated_at')),
            ExportColumn::make('deleted_at')
                ->label(__('common.deleted_at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if (($failedRowsCount = $export->getFailedRowsCount()) !== 0) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
