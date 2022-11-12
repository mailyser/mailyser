<div>
    <form wire:submit.prevent="updateQuantity">
        <x-filament::card>
            <h2 class="text-xl font-semibold">
                Your subscription
            </h2>

            <p>
                <span class="text-sm">x</span>{{ $user->subscription()->quantity }} <u>Sender Addresses</u>
            </p>

            <div class="max-w-[280px]">
                {{ $this->form }}
            </div>

            <x-filament::button wire:target="updateQuantity"
                                wire:loading.attr="disabled"
                                type="submit"
            >
                Update Sender Quantity
            </x-filament::button>
        </x-filament::card>
    </form>

    <x-filament::card class="mt-6">
        <p class="max-w-lg">
            {{ config('app.name') }} uses Stripe as billing partner, you will be redirected to Stripe to manage your subscription and invoices.
        </p>

        <x-filament::button wire:click.prevent="manageSubscription"
                            wire:target="manageSubscription"
                            wire:loading.attr="disabled"
        >
            Manage Subscription
        </x-filament::button>
    </x-filament::card>
</div>
