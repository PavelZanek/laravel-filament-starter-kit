<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Override;

final class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
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
            Actions\CreateAction::make(),
        ];
    }
}
