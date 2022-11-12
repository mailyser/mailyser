<?php

namespace App\Observers;

use App\Models\Sender;

class SenderObserver
{
    public function creating(Sender $sender)
    {
        $sender->user()->associate(auth()->user());
    }
}
