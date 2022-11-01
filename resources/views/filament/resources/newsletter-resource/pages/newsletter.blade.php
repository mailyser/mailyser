<x-filament::page :widget-data="['record' => $record]">
    @switch($record->status)
        @case(\App\Enums\NewsletterStatusEnum::Draft->name)
            @include('filament.resources.newsletter-resource.partials.downloadContacts')
            @break
        @case(\App\Enums\NewsletterStatusEnum::Waiting->name)
            @include('filament.resources.newsletter-resource.partials.sendCampaign')
            @break
        @case(\App\Enums\NewsletterStatusEnum::Sent->name)
            @include('filament.resources.newsletter-resource.partials.startScan')
            @break
        @case(\App\Enums\NewsletterStatusEnum::Scanning->name)
        @case(\App\Enums\NewsletterStatusEnum::Finished->name)
            @include('filament.resources.newsletter-resource.partials.scanningOrFinished')
            @break
    @endswitch
</x-filament::page>
