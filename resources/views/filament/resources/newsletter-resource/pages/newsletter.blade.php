<x-filament::page>
    @if($canBeScanned)
        <x-filament::card>
            <x-filament::card.heading>
                Start scanning
            </x-filament::card.heading>

            <p>
                lalalal
            </p>

            <x-filament::button wire:click.prevent="downloadContacts">
                Scan
            </x-filament::button>
        </x-filament::card>
    @else
        <x-filament::card>
            <x-filament::card.heading>
                Your audience sample
            </x-filament::card.heading>

            <p>
                First of all, download your audience details in order to send your nesletter to them.
            </p>

            <x-filament::button wire:click.prevent="downloadContacts">
                Download Contacts
            </x-filament::button>
        </x-filament::card>
    @endif
</x-filament::page>
