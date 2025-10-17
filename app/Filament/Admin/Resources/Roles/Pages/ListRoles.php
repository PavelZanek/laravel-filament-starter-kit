<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Pages;

use App\Filament\Admin\Resources\Roles\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    #[Override]
    public function getTitle(): string
    {
        return __('admin/role-resource.list.title');
    }

    public function setPage($page, $pageName = 'page'): void // @phpstan-ignore-line @pest-ignore-type
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
