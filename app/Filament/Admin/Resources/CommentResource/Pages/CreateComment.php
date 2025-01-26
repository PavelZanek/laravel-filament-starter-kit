<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommentResource\Pages;

use App\Filament\Admin\Resources\CommentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class CreateComment extends CreateRecord
{
    protected static string $resource = CommentResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
    {
        return __('admin/comment-resource.create.title');
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        /** @var string $url */
        $url = $this->getResource()::getUrl('index');

        return $url;
    }

    #[Override]
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('admin/comment-resource.flash.created');
    }
}
