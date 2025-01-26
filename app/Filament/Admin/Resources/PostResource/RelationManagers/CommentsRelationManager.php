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

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

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
                Forms\Components\Select::make('user_id')
                    ->label(__('admin/comment-resource.attributes.user_id'))
                    ->relationship('user', 'email')
                    ->preload()
                    ->nullable()
                    ->default(auth()->id())
                    ->searchable(),
                Forms\Components\TextInput::make('content')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('admin/user-resource.attributes.email'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->label(__('admin/comment-resource.attributes.content'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('admin/comment-resource.create.title'))
                    ->successNotificationTitle(__('admin/comment-resource.flash.created')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->modalHeading(__('admin/comment-resource.edit.title'))
                    ->successNotificationTitle(__('admin/comment-resource.flash.updated')),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->successNotificationTitle(__('admin/comment-resource.flash.deleted')),
            ])
            ->bulkActions([
                //
            ]);
    }
}
