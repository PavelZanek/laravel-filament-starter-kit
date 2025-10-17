<?php

declare(strict_types=1);

namespace App\Filament\Exports\Admin;

use App\Filament\Exports\BaseExporter;
use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

final class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return BaseExporter::mergeColumns(self::getResourceColumns());
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return BaseExporter::getCompletedNotificationBody($export, 'user');
    }

    /**
     * @return array<ExportColumn>
     */
    private static function getResourceColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('admin/user-resource.attributes.name')),
            ExportColumn::make('email')
                ->label(__('admin/user-resource.attributes.email')),
            ExportColumn::make('email_verified_at')
                ->label(__('admin/user-resource.attributes.email_verified_at')),
            ExportColumn::make('roles.name')
                ->label(__('admin/user-resource.custom_attributes.role')),
        ];
    }
}
