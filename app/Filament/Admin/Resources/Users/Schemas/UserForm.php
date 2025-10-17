<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;

final class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('admin/user-resource.attributes.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('admin/user-resource.attributes.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label(__('admin/user-resource.attributes.password'))
                    ->required()
                    ->password()
                    ->maxLength(255)
                    ->rule(Password::default())
                    ->visibleOn('create'),
                Select::make('roles')
                    ->label(__('admin/user-resource.custom_attributes.role'))
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query): Builder {
                            $superAdminRole = Role::query()
                                ->where('name', Role::SUPER_ADMIN)
                                ->where('guard_name', Role::GUARD_NAME_WEB)
                                ->firstOrFail();

                            return $query->whereKeyNot($superAdminRole->getKey());
                        }
                    )
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->required()
                    ->minItems(1)
                    ->maxItems(1)
                    ->hidden(fn (?User $record): bool => $record?->hasRole(Role::SUPER_ADMIN) ?? false),
            ]);
    }
}
