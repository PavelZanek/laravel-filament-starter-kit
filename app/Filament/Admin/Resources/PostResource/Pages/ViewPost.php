<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected static string $view = 'filament.admin.resources.posts.pages.view';

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
    {
        return __('admin/post-resource.view.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
