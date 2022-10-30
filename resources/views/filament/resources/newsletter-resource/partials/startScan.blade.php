<x-filament::card.heading>
    <span class="mr-2 inline-flex items-center justify-center h-8 w-8 bg-primary-600 rounded-full">
        3
    </span>
    Scan audience
</x-filament::card.heading>

<div>
    <p class="mb-8 max-w-xl mx-auto">
        Once your email software finishes sending all the campaign emails, click the button below to start scanning for the results.
    </p>

    <x-filament::button wire:click.prevent="startScan">
        <span class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Start Scan
        </span>
    </x-filament::button>
</div>
