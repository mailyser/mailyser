<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cashier::calculateTaxes();

        Filament::serving(function () {
            Filament::registerTheme(
                app(Vite::class)('resources/css/app.css'),
            );

            Filament::registerNavigationItems([
                NavigationItem::make('Profile')
                    ->url(route('filament.pages.my-profile'), shouldOpenInNewTab: false)
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.pages.my-profile'))
                    ->icon('heroicon-o-user')
                    ->group('Account')
                    ->sort(1),

                NavigationItem::make('Support & Feedback')
                    ->url('mailto:support@mailyser.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-support')
                    ->group('Support')
                    ->sort(3),
            ]);
        });
    }
}
