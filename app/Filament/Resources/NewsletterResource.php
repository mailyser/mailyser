<?php

namespace App\Filament\Resources;

use App\Enums\NewsletterStatusEnum;
use App\Filament\Resources\NewsletterResource\Pages;
use App\Http\Middleware\SubscriptionMiddleware;
use App\Models\Newsletter;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\NewsletterResource\Pages\ManageNewsletter as ManageNewsletter;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Newsletters';

    protected static string|array $middlewares = [
        SubscriptionMiddleware::class,
    ];

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereBelongsTo(auth()->user());
    }

//    protected function getRedirectUrl(Model $record): string
//    {
//        return $this->getResource()::generateUrl('index');
//    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Internal Name')
                    ->placeholder('My Campaign Test')
                    ->required(),
                TextInput::make('email')
                    ->label('Sender Address')
                    ->placeholder('example@newsletter.com')
                    ->email()
                    ->required(),
                TextInput::make('keyword')
                    ->placeholder('Thanks for reading our october 2022 newsletter!')
                    ->helperText('Use a word or sentence present in your email template. We use this text, together with the sender address to identify the email in the mailbox. ')
                    ->required()
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('keyword'),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum(NewsletterStatusEnum::cases())
                    ->colors(NewsletterStatusEnum::badgeColors())
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('edit')
                    ->label('View')
                    ->color('secondary')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Newsletter $record) => route('filament.resources.newsletters.manage', $record)),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll();
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
            'manage' => ManageNewsletter::route('/{record}/manage'),
        ];
    }
}
