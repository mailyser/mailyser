<x-filament::page>
    <x-filament::card>
        @if($user->subscribed())
            <h2 class="text-xl font-semibold">
                Your subscription
            </h2>

            <div>
                Sub: Print subscription information here
            </div>

            <p class="max-w-lg">
                {{ config('app.name') }} uses Stripe as billing partner, you will be redirected to Stripe to manage your subscription and invoices.
            </p>

            <x-filament::button wire:click.prevent="manageSubscription"
                                wire:target="manageSubscription"
                                wire:loading.attr="disabled"
            >
                Manage Subscription
            </x-filament::button>
        @else
            <h2 class="text-xl font-semibold">
                Get your subscription
            </h2>

            <p>
                You will be able to manage your subscription once the beta ends.
            </p>
        @endif
    </x-filament::card>

    @if(! $user->subscribed())
    <div class="grid grid-cols-3 gap-4">
        @foreach($plans as $plan)
            <x-filament::card>
                <x-filament::card.heading>
                    {{ $plan['name'] }}
                </x-filament::card.heading>

                <div>
                    sadjdkabskhjdbaskjdjka
                </div>

                <x-filament::button wire:click.prevent="checkout('{{ $plan['stripe_id'] }}')"
                                    wire:target="checkout('{{ $plan['stripe_id'] }}')"
                                    wire:loading.attr="disabled"
                                    class="w-full"
                >
                    Start Free Trial
                </x-filament::button>
            </x-filament::card>
        @endforeach
    </div>
    @endif
</x-filament::page>
