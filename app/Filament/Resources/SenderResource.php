<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SenderResource\Pages;
use App\Http\Middleware\SubscriptionMiddleware;
use App\Models\Sender;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SenderResource extends Resource
{
    protected static ?string $model = Sender::class;

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static ?string $navigationGroup = 'Newsletters';

    protected static ?int $navigationSort = 1;

    protected static string|array $middlewares = [
        SubscriptionMiddleware::class,
    ];

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereBelongsTo(auth()->user());
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return (bool) auth()->user()->senders->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email_address')
                    ->maxLength(255)
                    ->email()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email_address')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->sortable()
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSenders::route('/'),
        ];
    }
}
