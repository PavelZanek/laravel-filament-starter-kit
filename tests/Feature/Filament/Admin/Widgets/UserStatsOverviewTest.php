<?php

declare(strict_types=1);

use App\Filament\Admin\Widgets\UserStatsOverview;
use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('user stats overview widget', function (): void {
    User::factory()->count(3)->withRole()->create();
    User::factory()->count(2)->withRole(Role::ADMIN)->create();
    User::factory()->withRole(Role::SUPER_ADMIN)->create();

    $widget = new UserStatsOverview;
    $widgetData = $widget->getStatsData();

    expect($widgetData)->toHaveCount(3)
        ->and($widgetData[0]['label'])->toBe(__('admin/user-resource.widgets.user_stats_overview.all_users'))
        ->and($widgetData[0]['value'])->toBe(6)
        ->and($widgetData[1]['label'])->toBe(__('admin/user-resource.widgets.user_stats_overview.admins'))
        ->and($widgetData[1]['value'])->toBe(2)
        ->and($widgetData[2]['label'])->toBe(__('admin/user-resource.widgets.user_stats_overview.customers'))
        ->and($widgetData[2]['value'])->toBe(3);
});

test('can view widget with permission', function (string $role): void {
    $user = User::factory()->withWorkspaces()->withRole($role)->create();
    actingAs($user);

    expect(UserStatsOverview::canView())->toBeTrue();
})->with([Role::ADMIN, Role::SUPER_ADMIN]);

test('cannot view widget without permission', function (): void {
    $user = User::factory()->withWorkspaces()->withRole(Role::AUTHENTICATED)->create();
    actingAs($user);

    expect(UserStatsOverview::canView())->toBeFalse();
});

test('cannot view widget when not authenticated', function (): void {
    expect(UserStatsOverview::canView())->toBeFalse();
});
