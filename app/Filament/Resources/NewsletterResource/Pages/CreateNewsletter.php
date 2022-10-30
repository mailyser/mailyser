<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use App\Filament\Resources\NewsletterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletter extends CreateRecord
{
    protected static string $resource = NewsletterResource::class;

    protected function getRedirectUrl(): string
    {
        return route('filament.resources.newsletters.manage', $this->record);
    }
}
