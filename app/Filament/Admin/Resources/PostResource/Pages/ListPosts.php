<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
    {
        return __('admin/post-resource.list.title');
    }

    public function setPage($page, $pageName = 'page'): void // @phpstan-ignore-line @pest-ignore-type
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
