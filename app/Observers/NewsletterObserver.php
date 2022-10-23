<?php

namespace App\Observers;

use App\Models\Newsletter;
use Illuminate\Support\Str;

class NewsletterObserver
{
    public function creating(Newsletter $newsletter)
    {
        $newsletter->uuid = Str::uuid()->toString();
        $newsletter->user()->associate(auth()->user());

        // spatie statuses initial
    }
}
