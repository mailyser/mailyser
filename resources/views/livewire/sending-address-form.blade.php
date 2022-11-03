<x-filament-breezy::grid-section class="mt-8">

    <x-slot name="title">
        Sending address
    </x-slot>

    <x-slot name="description">
        This is the email address you need to use to send your newsletter campaigns.
    </x-slot>

    <form wire:submit.prevent="submit" class="col-span-2 sm:col-span-1 mt-5 md:mt-0">
        <x-filament::card>

            {{ $this->form }}

            <p class="text-sm">
                <strong>Can only be set once</strong>. Contact us if you need to update the sending address.
            </p>

            @if(! $this->email)
                <x-slot name="footer">
                    <div class="text-right">
                        <x-filament::button type="submit">
                            {{ __('filament-breezy::default.profile.personal_info.submit.label') }}
                        </x-filament::button>
                    </div>
                </x-slot>
            @endif
        </x-filament::card>
    </form>

</x-filament-breezy::grid-section>
