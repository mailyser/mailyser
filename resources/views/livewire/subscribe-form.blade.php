<div>
    <form wire:submit.prevent="checkout('{{ $plan['stripe_id'] }}')">
    <x-filament::card>
        <h2 class="text-xl font-semibold">
            Get your subscription
        </h2>

        <p>
            You need a subscription to use Mailyser. Try it for free, no card up-front required.
        </p>

        <div>
            $ <span class="text-4xl font-semibold">99</span> <span class="text-base">/mo/sender*</span>
        </div>

        <div class="max-w-xs">
            {{ $this->form }}
        </div>

        <x-filament::button wire:target="checkout('{{ $plan['stripe_id'] }}')"
                            wire:loading.attr="disabled"
                            type="submit"
        >
            Start Free Trial
        </x-filament::button>
    </x-filament::card>
    </form>
</div>
