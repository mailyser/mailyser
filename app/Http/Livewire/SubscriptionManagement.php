<?php

namespace App\Http\Livewire;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class SubscriptionManagement extends Component implements HasForms
{
    use InteractsWithForms;

    public User $user;

    public $quantity;

    public function mount()
    {
        /** @var User $user */
        $user = auth()->user();

        $this->user = $user;

        $this->quantity = $user->subscription()->quantity;
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('quantity')
                ->label('Update sender quantity')
                ->helperText('$99/mo/sender. $99 x '.$this->newPrice)
                ->numeric()
                ->minValue(1)
                ->required()
                ->reactive(),
        ];
    }

    public function getNewPriceProperty()
    {
        $quantity = $this->quantity;

        if (! $quantity || $quantity < 1) {
            $quantity = 1;
        }

        return $quantity.' = $'.($quantity * 99);
    }

    public function manageSubscription(): void
    {
        $billingPortalUrl = $this->user->billingPortalUrl(
            route('filament.pages.subscription')
        );

        $this->redirect($billingPortalUrl);
    }

    public function updateQuantity()
    {
        $this->user->subscription()->updateQuantity($this->quantity);

        Notification::make()
            ->title('Sender quantity updated')
            ->success()
            ->send();
    }
}
