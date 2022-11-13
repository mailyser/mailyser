<?php

namespace App\Http\Livewire;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Str;
use Laravel\Cashier\Checkout;
use Livewire\Component;

class SubscribeForm extends Component implements HasForms
{
    use InteractsWithForms;

    public User $user;

    public $quantity = 1;

    public $plan;

    public function mount()
    {
        /** @var User $user */
        $user = auth()->user();

        $this->user = $user;

        $this->plan = SubscriptionPlan::query()
            ->where('active', true)
            ->first()
            ->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('quantity')
                ->label('How many senders?')
                ->helperText('*A sender is the email address where you send the newsletters from.')
                ->numeric()
                ->minValue(1)
                ->required(),
        ];
    }

    /**
     * @throws Exception
     */
    public function checkout(string $stripeId): Checkout
    {
        $checkoutSessionId = Str::random();

        /** @var User $user */
        $user = $this->user;

        // check quantity is greater than 1

        return $user
            ->newSubscription('default', $stripeId)
            ->trialDays(8)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('filament.pages.checking-subscription').'?checkout_session='.$checkoutSessionId,
                'cancel_url' => route('filament.pages.subscription'),
                'client_reference_id' => $checkoutSessionId,
                'line_items' => [[
                    'price' => $stripeId,
                    'adjustable_quantity' => [
                        'enabled' => true,
                        'minimum' => 1,
                    ],
                    'quantity' => $this->quantity,
                ]],
            ]);
    }
}
