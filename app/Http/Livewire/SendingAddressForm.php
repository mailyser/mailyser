<?php

namespace App\Http\Livewire;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Notifications\Notifiable;
use Livewire\Component;

class SendingAddressForm extends Component implements HasForms
{
    use InteractsWithForms;

    public int $userId;

    public ?string $email = null;

    public bool $disabled = false;

    public function mount(): void
    {
        $user = auth()->user();

        $this->userId = $user->id;
        $this->email = $user->sending_address;
        if (filled($user->sending_address)) {
            $this->disabled = true;
        }
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->email()
                ->required()
                ->disabled($this->disabled),
        ];
    }

    public function submit(): void
    {
        $user = User::find($this->userId);

        // if not filled already
        abort_if(filled($user->sending_address), 403);

        $user->update([
            'sending_address' => $this->email,
        ]);

        $this->disabled = true;

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }
}
