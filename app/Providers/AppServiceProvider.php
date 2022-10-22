<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;

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
        Filament::serving(function () {
            Filament::registerNavigationItems([
                NavigationItem::make('Profile')
                    ->url(route('filament.pages.my-profile'), shouldOpenInNewTab: false)
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.pages.my-profile'))
                    ->icon('heroicon-o-user')
                    ->group('Account')
                    ->sort(1),
                NavigationItem::make('Subscription')
                    ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-credit-card')
                    ->group('Account')
                    ->sort(2),

                NavigationItem::make('Support & Feedback')
                    ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-support')
                    ->group('Support')
                    ->sort(1),
            ]);
        });
    }
}
