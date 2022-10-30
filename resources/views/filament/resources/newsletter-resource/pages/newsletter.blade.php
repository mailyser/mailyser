<x-filament::page>
    <x-filament::card class="text-center py-12">
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
                @include('filament.resources.newsletter-resource.partials.scanning')
                @break
        @endswitch
    </x-filament::card>
</x-filament::page>
