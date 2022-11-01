<x-filament::card class="text-center py-12">
    <x-filament::card.heading>
        <span class="mr-2 inline-flex items-center justify-center h-8 w-8 bg-primary-600 text-white rounded-full">
            2
        </span>
        Send your campaign
    </x-filament::card.heading>

    <div>
        <p class="mb-8 max-w-xl mx-auto">
            Send your newsletter campaign to the contacts you have downloaded from the previous step. Once this is done, click the button below.
        </p>

        <x-filament::button wire:click.prevent="campaignSent">
            <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                I have sent the campaign
            </span>
        </x-filament::button>
    </div>
</x-filament::card>
