<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CommentResource\Pages;

use App\Filament\Admin\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class EditComment extends EditRecord
{
    protected static string $resource = CommentResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
    {
        return __('admin/comment-resource.edit.title');
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        /** @var string $url */
        $url = $this->getResource()::getUrl('index');

        return $url;
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->modalHeading(__('admin/comment-resource.actions.modals.delete.single.heading'))
                ->modalDescription(__('admin/comment-resource.actions.modals.delete.single.description'))
                ->successNotificationTitle(__('admin/comment-resource.flash.deleted')),
            Actions\ForceDeleteAction::make()
                ->modalHeading(__('admin/comment-resource.actions.modals.force_delete.single.heading'))
                ->modalDescription(__('admin/comment-resource.actions.modals.force_delete.single.description'))
                ->successNotificationTitle(__('admin/comment-resource.flash.force_deleted')),
            Actions\RestoreAction::make()
                ->modalHeading(__('admin/comment-resource.actions.modals.restore.single.heading'))
                ->modalDescription(__('admin/comment-resource.actions.modals.restore.single.description'))
                ->successNotificationTitle(__('admin/comment-resource.flash.restored')),
        ];
    }

    #[Override]
    protected function getSavedNotificationTitle(): ?string
    {
        return __('admin/comment-resource.flash.updated');
    }
}
