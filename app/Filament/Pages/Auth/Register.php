<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Checkbox;
use JeffGreco13\FilamentBreezy\Http\Livewire\Auth\Register as FilamentBreezyRegister;

class Register extends FilamentBreezyRegister
{
    public bool $consent_to_terms = false;

    protected function getFormSchema(): array
    {
        return array_merge(parent::getFormSchema(), [
            Checkbox::make('consent_to_terms')
                ->label('I consent to the terms of service and privacy policy.')
                ->required(),
        ]);
    }

    protected function prepareModelData($data): array
    {
        $preparedData = parent::prepareModelData($data);
        $preparedData['consent_to_terms'] = $this->consent_to_terms;

        return $preparedData;
    }
}
