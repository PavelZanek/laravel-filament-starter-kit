<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommentResource\Pages;

use App\Filament\Admin\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
    {
        return __('admin/comment-resource.list.title');
    }

    public function setPage($page, $pageName = 'page'): void // @phpstan-ignore-line @pest-ignore-type
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
