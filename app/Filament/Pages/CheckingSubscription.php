<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CheckingSubscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.checking-subscription';

    protected static bool $shouldRegisterNavigation = false;

    public function mount()
    {
        $checkoutSession = session()->get('checkout_session');

        if (is_null($checkoutSession)) {
            $this->redirect(
                route('filament.pages.subscription')
            );
        }

//        abort_if(session()->get('checkout_session'), 404);
    }

    public function checkSubscriptionStatus()
    {
        if (auth()->user()->subscribed()) {
            $this->subscribed();
        }
    }

    public function subscribed()
    {
        // @todo throw confetti

        session()->remove('checkout_session');

        $this->redirect(
            route('filament.pages.subscription')
        );
    }
}
