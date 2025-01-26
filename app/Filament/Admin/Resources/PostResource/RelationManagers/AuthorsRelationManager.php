<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PostResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class AuthorsRelationManager extends RelationManager
{
    protected static string $relationship = 'authors';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin/post-resource.relationships.'.self::$relationship);
    }

    #[Override]
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order')
                    ->label(__('common.order'))
                    ->required()
                    ->integer(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('order')
                            ->integer()
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button(),
                Tables\Actions\DetachAction::make()
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
