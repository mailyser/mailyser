<x-filament::card>
    <x-filament::card.heading>
        {{ $record->finishedScanning() ? 'Scan completed' : 'Scanning...' }}
    </x-filament::card.heading>

    <div>
        <ul class="space-y-2">
            <li>
                Sender: <u>{{ $record->email }}</u>
            </li>
            <li>
                Keyword: <u>{{ $record->keyword }}</u>
            </li>
        </ul>
    </div>
</x-filament::card>

