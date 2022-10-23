<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use App\Enums\NewsletterStatusEnum;
use App\Filament\Resources\NewsletterResource;
use App\Models\Email;
use App\Models\Newsletter;
use Exception;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use Spatie\ModelStatus\Exceptions\InvalidStatus;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManageNewsletter extends Page
{
    use AuthorizesRequests;

    protected static string $resource = NewsletterResource::class;

    protected static string $view = 'filament.resources.newsletter-resource.pages.newsletter';

    public Newsletter $record;

    public bool $canBeScanned = false;

    /**
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            Action::make('Audience')
                ->color('secondary')
                ->icon('heroicon-o-download')
                ->action('downloadContacts'),
        ];
    }

    /**
     * @throws AuthorizationException
     */
    public function mount()
    {
        $this->authorize('manage', $this->record);

        $this->canBeScanned = $this->record->status === NewsletterStatusEnum::Prepared->name;
    }

    public function getBreadcrumb(): ?string
    {
        return $this->record->name;
    }

    protected function getTitle(): string
    {
        return $this->record->name;
    }

    /**
     * @throws AuthorizationException
     * @throws InvalidStatus
     * @throws CannotInsertRecord
     */
    public function downloadContacts(): StreamedResponse
    {
        $this->authorize('manage', $this->record);

        $columns = [
            'first_name',
            'last_name',
            'email',
        ];
        // if first time, assign contacts. save in a pivot table.
        $audience = Email::query()->select($columns)->get()->toArray();

        $csv = Writer::createFromFileObject(new SplTempFileObject);
        $csv->insertOne($columns);
        $csv->insertAll($audience);

        if ($this->record->status === NewsletterStatusEnum::Draft->name) {
            $this->record->setStatus(NewsletterStatusEnum::Prepared->name);
            $this->canBeScanned = true;
        }

//        Notification::make()
//            ->title('Downloading contacts')
//            ->success()
//            ->send();

        // add a flag

        return response()->streamDownload(function () use ($csv) {
            $csv->output('audience.csv');
        }, 'audience.csv');
    }
}
