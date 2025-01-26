<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
    {
        return __('admin/post-resource.edit.title');
    }

    protected function getRedirectUrl(): string
    {
        /** @var string $url */
        $url = $this->getResource()::getUrl('index');

        return $url;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
