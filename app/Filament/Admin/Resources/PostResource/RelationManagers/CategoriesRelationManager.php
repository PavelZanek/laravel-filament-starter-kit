<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PostResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Override;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

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
                Forms\Components\TextInput::make('name')
                    ->label(__('admin/category-resource.attributes.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        fn (Forms\Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))
                    ),
                Forms\Components\TextInput::make('slug')
                    ->label(__('admin/category-resource.attributes.slug'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('admin/category-resource.create.title'))
                    ->successNotificationTitle(__('admin/category-resource.flash.created')),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->modalHeading(__('admin/category-resource.edit.title'))
                    ->successNotificationTitle(__('admin/category-resource.flash.updated')),
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
