<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TagResource\Pages;

use App\Filament\Admin\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
    {
        return __('admin/tag-resource.list.title');
    }

    public function setPage($page, $pageName = 'page'): void // @phpstan-ignore-line @pest-ignore-type
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalHeading(__('admin/tag-resource.create.title'))
                ->successNotificationTitle(__('admin/tag-resource.flash.created')),
        ];
    }
}
