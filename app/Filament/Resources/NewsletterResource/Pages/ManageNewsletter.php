<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use App\Actions\GenerateNewsletterAudienceAction;
use App\Enums\NewsletterStatusEnum;
use App\Filament\Resources\NewsletterResource;
use App\Models\Email;
use App\Models\Newsletter;
use Carbon\Carbon;
use Exception;
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
                ->action('downloadContacts')
                ->visible(fn (): bool => NewsletterStatusEnum::canDownloadContacts($this->record->status)),
        ];
    }

    /**
     * @throws AuthorizationException
     */
    public function mount()
    {
        $this->authorize('manage', $this->record);

        $this->canBeScanned = $this->record->status === NewsletterStatusEnum::Sent->name;
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

        $csv = Writer::createFromFileObject(new SplTempFileObject);
        $csv->insertOne(Newsletter::audienceFields());
        $csv->insertAll($this->record->getOrGenerateAudience());

        if ($this->record->status === NewsletterStatusEnum::Draft->name) {
            $this->record->setStatus(NewsletterStatusEnum::Waiting->name);
        }

        return response()->streamDownload(function () use ($csv) {
            $csv->output('audience.csv');
        }, 'audience.csv');
    }

    /**
     * @throws InvalidStatus
     * @throws AuthorizationException
     */
    public function campaignSent()
    {
        $this->authorize('manage', $this->record);

        $this->record->setStatus(NewsletterStatusEnum::Sent->name);
    }

    /**
     * @throws InvalidStatus
     * @throws AuthorizationException
     */
    public function startScan()
    {
        $this->authorize('manage', $this->record);

        $this->record->update([
            'scheduled_for' => Carbon::now()->addMinutes(5),
        ]);

        $this->record->setStatus(NewsletterStatusEnum::Scanning->name);
    }
}
