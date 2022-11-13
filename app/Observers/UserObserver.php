<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user)
    {
        if (! $user->password) {
            $user->password = Hash::make(Str::random());
        }
    }
}
