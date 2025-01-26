<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
    {
        return __('admin/post-resource.create.title');
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        /** @var string $url */
        $url = $this->getResource()::getUrl('index');

        return $url;
    }
}
