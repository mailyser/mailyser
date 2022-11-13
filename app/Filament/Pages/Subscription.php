<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class Subscription extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.pages.subscription';

    protected static ?string $navigationGroup = 'Account';

    protected static ?int $navigationSort = 2;

    public $user;

    public function mount()
    {
        /** @var User $user */
        $user = auth()->user();
        $this->user = $user;
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
}
