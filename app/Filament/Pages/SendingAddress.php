<?php

namespace App\Filament\Pages;

use App\Http\Middleware\SubscriptionMiddleware;
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

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static ?string $navigationGroup = 'Newsletters';

    protected static ?string $navigationLabel = 'Senders';

    protected static ?int $navigationSort = 1;

    protected static string|array $middlewares = [
        SubscriptionMiddleware::class,
    ];

    protected ?string $heading = 'Define a sending address';

    public ?string $email = null;

    protected static function shouldRegisterNavigation(): bool
    {
        return ! (bool) auth()->user()->senders->count();
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

    public function createSender()
    {
        auth()->user()->senders()->create([
            'email_address' => $this->email,
            'enabled' => true,
        ]);

        Notification::make()
            ->title('Sender created')
            ->success()
            ->send();

        $this->redirect(
            route('filament.resources.senders.index')
        );
    }
}
