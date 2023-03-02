<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use App\Enums\NewsletterStatusEnum;
use App\Filament\Resources\NewsletterResource;
use App\Models\Newsletter;
use Carbon\Carbon;
use Exception;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewNewsletter extends ViewRecord
{
    use AuthorizesRequests;

    protected static string $resource = NewsletterResource::class;

    protected static string $view = 'filament.resources.newsletter-resource.pages.newsletter';

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    protected function getTitle(): string
    {
        return $this->record->name;
    }

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
     * @return StreamedResponse
     * @throws AuthorizationException
     * @throws CannotInsertRecord
     */
    public function downloadContacts(): StreamedResponse
    {
        $this->authorize('manage', $this->record);

        $csv = Writer::createFromFileObject(new SplTempFileObject);
        $csv->insertOne(Newsletter::audienceFields());
        if($this->record->has_mail_tester) {
            $csv->insertOne(['mail','receiver',$this->record->getMailTestUniqueEmail() ]);
        }
        
        $csv->insertAll($this->record->getOrGenerateAudience());

        
        if ($this->record->status === NewsletterStatusEnum::Draft->name) {
            $this->record->setStatus(NewsletterStatusEnum::Waiting->name);
        }

        return response()->streamDownload(function () use ($csv) {
            $csv->output('audience.csv');
        }, 'audience.csv');
    }

    /**
     * @throws AuthorizationException
     */
    public function campaignSent()
    {
        $this->authorize('manage', $this->record);

        $this->record->setStatus(NewsletterStatusEnum::Sent->name);
    }

    /**
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

    protected function getFooterWidgets(): array
    {
        $widgets = [];

        if (in_array($this->record->status, [
            NewsletterStatusEnum::Scanning->name,
            NewsletterStatusEnum::Finished->name,
        ])) {
            $widgets = array_merge($widgets, [
                NewsletterResource\Widgets\NewsletterSpamReportChart::class,
                NewsletterResource\Widgets\NewsletterDeliveryReportChart::class,
            ]);
        }

        return $widgets;
    }
}
