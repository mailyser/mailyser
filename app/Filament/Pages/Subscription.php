<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Subscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.pages.subscription';

    protected static ?string $navigationGroup = 'Account';

    protected static ?int $navigationSort = 2;

    protected static function getNavigationBadge(): ?string
    {
        if (auth()->user()->subscribed()) {
            return null;
        }

        return '!';
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
