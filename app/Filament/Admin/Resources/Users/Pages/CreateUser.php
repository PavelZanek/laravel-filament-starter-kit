<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Override;

final class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string
    {
        return __('admin/user-resource.create.title');
    }

    #[Override] // @phpstan-ignore-line
    public function getSubheading(): ?string
    {
        return __('admin/user-resource.create.subheading');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var string $psw */
        $psw = $data['password'];
        $data['password'] = Hash::make($psw);

        return $data;
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        /** @var string $url */
        $url = $this->getResource()::getUrl('index');

        return $url;
    }
}
