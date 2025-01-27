<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Role;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Override;

final class UserStatsOverview extends BaseWidget
{
    /**
     * @return array<array-key, mixed>
     */
    public function getStatsData(): array
    {
        return collect($this->getStats())->map(fn (Stat $stat): array => [
            'label' => $stat->getLabel(),
            'value' => $stat->getValue(),
        ])->toArray();
    }

    #[Override]
    protected function getStats(): array
    {
        return [
            Stat::make(
                __('admin/user-resource.widgets.user_stats_overview.all_users'),
                User::query()->count()
            ),
            Stat::make(
                __('admin/user-resource.widgets.user_stats_overview.admins'),
                User::query()->role(Role::ADMIN)->count()
            ),
            Stat::make(
                __('admin/user-resource.widgets.user_stats_overview.customers'),
                User::query()->role(Role::AUTHENTICATED)->count()
            ),
        ];
    }
}
