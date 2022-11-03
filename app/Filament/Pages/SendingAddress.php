<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SendingAddress extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.sending-address';

    protected static ?string $navigationIcon = 'heroicon-o-mail';

    protected static ?string $navigationGroup = 'Newsletters';

    protected static ?string $navigationLabel = 'Newsletters';

    protected ?string $heading = 'Define a sending address';

    public ?string $email = null;

    protected static function shouldRegisterNavigation(): bool
    {
        return ! filled(auth()->user()->sending_address);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('email')
                        ->email()
                        ->placeholder('Email you send the newsletters from')
                        ->required()
                        ->columnSpan(1),
                ]),
        ];
    }

    public function updateSendingEmail()
    {
        $user = auth()->user();

        abort_if(filled($user->sending_address), 403);

        $user->update([
            'sending_address' => $this->email,
        ]);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();

        $this->redirect(
            route('filament.resources.newsletters.index')
        );
    }
}
