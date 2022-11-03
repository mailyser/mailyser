<x-filament::page>
    <form wire:submit.prevent="updateSendingEmail" class="col-span-2 sm:col-span-1 mt-5 md:mt-0">
        <x-filament::card>
            <p class="max-w-lg">
                To create your first newsletter campaign, you'll <strong>need to set an email address</strong> as your default sending address.
            </p>
            <p>
                We'll use this address to scan the results of your campaigns.
            </p>

            {{ $this->form }}

            <p class="mt-2">
                <strong>Can only be set once</strong>.
            </p>

            @if(! $this->email)
                <x-slot name="footer">
                    <div class="text-right">
                        <x-filament::button type="submit">
                            Submit
                        </x-filament::button>
                    </div>
                </x-slot>
            @endif
        </x-filament::card>
    </form>
</x-filament::page>
