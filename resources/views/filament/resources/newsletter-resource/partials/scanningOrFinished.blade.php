<x-filament::card>
    <x-filament::card.heading>
        @if($record->finishedScanning())
            Scan completed
        @else
            <div class="flex items-center">
                <svg class="animate-spin mr-4 h-5 w-5 dark:text-white text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Scanning...
            </div>
        @endif
    </x-filament::card.heading>

    <div>
        <ul class="space-y-2">
            <li>
                Sender: <u>{{ $record->sender->email_address }}</u>
            </li>
            <li>
                Keyword: <u>{{ $record->keyword }}</u>
            </li>
        </ul>
    </div>
</x-filament::card>

