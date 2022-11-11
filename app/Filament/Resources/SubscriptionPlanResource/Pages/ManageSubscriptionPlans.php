<?php

namespace App\Filament\Resources\SubscriptionPlanResource\Pages;

use App\Filament\Resources\SubscriptionPlanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSubscriptionPlans extends ManageRecords
{
    protected static string $resource = SubscriptionPlanResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
