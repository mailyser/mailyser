<x-filament::card.heading>
    <span class="mr-2 inline-flex items-center justify-center h-8 w-8 bg-primary-600 rounded-full">
        1
    </span>
    Download the audience
</x-filament::card.heading>

<div>
    <p class="mb-8 max-w-xl mx-auto">
        First of all, download the audience file. This CSV file contains the information of the email accounts where you will need to send the newsletter to.
    </p>

    <x-filament::button wire:click.prevent="downloadContacts">
        <span class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download Contacts
        </span>
    </x-filament::button>
</div>
