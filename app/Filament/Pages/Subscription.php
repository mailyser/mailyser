<?php

namespace App\Filament\Pages;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Exception;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Laravel\Cashier\Checkout;
use Stripe\Stripe;
use Stripe\StripeClient;

class Subscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.pages.subscription';

    protected static ?string $navigationGroup = 'Account';

    protected static ?int $navigationSort = 2;

    public $user;

    public array $plans;

    public function mount()
    {
        $this->user = auth()->user();
        $this->plans = SubscriptionPlan::query()
            ->where('active', true)
            ->get()
            ->toArray();
    }

    protected static function getNavigationBadge(): ?string
    {
        if (auth()->user()->hasAccess()) {
            return null;
        }

        return '!';
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * @throws Exception
     */
    public function checkout(string $stripeId): Checkout
    {
//        $stripe = new StripeClient(config('cashier.secret'));

        session()->put('checkout_session', $checkoutSessionId = Str::random());

        return $this->user
            ->newSubscription('default', $stripeId)
            ->checkout([
                'success_url' => route('filament.pages.checking-subscription'),
                'cancel_url' => route('filament.pages.subscription'),
                'client_reference_id' => $checkoutSessionId,
            ]);
    }

    public function manageSubscription()
    {
        $billingPortalUrl = $this->user->billingPortalUrl(
            route('filament.pages.subscription')
        );

        $this->redirect($billingPortalUrl);
    }
}
