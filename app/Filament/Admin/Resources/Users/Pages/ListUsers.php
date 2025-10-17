<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\Role;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Override;

final class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    #[Override]
    public function getTitle(): string
    {
        return __('admin/user-resource.list.title');
    }

    public function setPage(int|string $page, ?string $pageName = 'page'): void // @phpstan-ignore-line @pest-ignore-type
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }

    /**
     * @return array|Tab[]
     */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make(__('common.all'))->badge($this->getModel()::count()),
        ];

        $roles = Role::query()
            ->withCount('users')
            ->where('is_default', true)
            ->get();

        foreach ($roles as $role) {
            $name = $role->name;
            $slug = str($name)->slug()->toString();

            $tabs[$slug] = Tab::make(Role::ROLES[$name])
                ->badge($role->users_count)
                ->modifyQueryUsing(function (Builder $query) use ($role): Builder {
                    // @codeCoverageIgnoreStart
                    return $query->role($role); // @phpstan-ignore-line
                    // @codeCoverageIgnoreEnd
                });
        }

        return $tabs;
    }

    /**
     * @return array|Actions\Action[]|Actions\ActionGroup[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
