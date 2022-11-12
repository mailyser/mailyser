<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\NewsletterStatusEnum;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NewslettersRelationManager extends RelationManager
{
    protected static string $relationship = 'newsletters';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('keyword')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('sender.email_address'),
                Tables\Columns\TextColumn::make('keyword'),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum(NewsletterStatusEnum::cases())
                    ->colors([
                        'primary' => static fn ($state): bool => in_array($state, [
                            NewsletterStatusEnum::Sent->name,
                        ]),
                        'secondary' => static fn ($state): bool => in_array($state, [
                            NewsletterStatusEnum::Draft->name,
                        ]),
                        'warning' => static fn ($state): bool => in_array($state, [
                            NewsletterStatusEnum::Waiting->name,
                            NewsletterStatusEnum::Scanning->name,
                        ]),
                        'success' => static fn ($state): bool => in_array($state, [
                            NewsletterStatusEnum::Finished->name
                        ]),
                    ])
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
