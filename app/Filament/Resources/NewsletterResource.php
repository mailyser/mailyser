<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterResource\Pages;
use App\Http\Middleware\SubscriptionMiddleware;
use App\Models\Newsletter;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Newsletters';

    protected static string|array $middlewares = [
        SubscriptionMiddleware::class,
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('keyword'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'edit' => Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }
}
