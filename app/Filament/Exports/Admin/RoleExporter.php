<?php

declare(strict_types=1);

namespace App\Filament\Exports\Admin;

use App\Filament\Exports\BaseExporter;
use App\Models\Role;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

final class RoleExporter extends Exporter
{
    protected static ?string $model = Role::class;

    public static function getColumns(): array
    {
        return BaseExporter::mergeColumns(self::getResourceColumns());
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return BaseExporter::getCompletedNotificationBody($export, 'role');
    }

    /**
     * @return array<ExportColumn>
     */
    private static function getResourceColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('filament-shield::filament-shield.column.name')),
            ExportColumn::make('guard_name')
                ->label(__('filament-shield::filament-shield.column.guard_name')),
            ExportColumn::make('team.name')
                ->label(__('filament-shield::filament-shield.column.team')),
            ExportColumn::make('permissions_count')
                ->label(__('filament-shield::filament-shield.column.permissions')),
            ExportColumn::make('users_count')
                ->label(__('admin/role-resource.custom_attributes.users_count')),
            ExportColumn::make('is_default')
                ->label(__('common.is_default')),
        ];
    }
}
