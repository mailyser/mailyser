<x-filament::card>
    <x-filament::card.heading>
        {{ $record->name }}
    </x-filament::card.heading>

    <div>
        @if($record->status === \App\Enums\NewsletterStatusEnum::Scanning->name)
            <p class="mb-8 max-w-xl mx-auto">
                We are scanning...

                [progress bar here]
            </p>
        @endif

        @if($record->status === \App\Enums\NewsletterStatusEnum::Finished->name)
            <p class="mb-8 max-w-xl mx-auto">
                completed.
            </p>
        @endif
    </div>
</x-filament::card>

