<x-filament::page>
    @if($user->subscribed())
        <livewire:subscription-management></livewire:subscription-management>
    @else
        <livewire:subscribe-form></livewire:subscribe-form>
    @endif
</x-filament::page>
