<?php

namespace App\Filament\Resources\SenderResource\Pages;

use App\Filament\Resources\SenderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSenders extends ManageRecords
{
    protected static string $resource = SenderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->disabled(! auth()->user()->hasAvailableSenders()),
            Actions\Action::make('Increase sender limit')
                ->icon('heroicon-o-plus')
                ->color('secondary')
                ->hidden(auth()->user()->hasAvailableSenders())
                ->url(route('filament.pages.subscription')),
        ];
    }
}
